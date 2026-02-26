<?php
// routes/web.php

use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\ForSaleController;
use App\Http\Controllers\Public\SectionController;
use App\Http\Controllers\Public\BookController as PublicBookController;
use App\Http\Controllers\Public\ItemController as PublicItemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('home');

Route::get('/change-language/{language}', function (string $language) {
    session(['language' => $language]);

    return back();
})->name('changeLanguage');

Route::get('/blog', [BlogController::class, 'showAllPosts'])->name('blog');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/for-sale', [ForSaleController::class, 'index'])->name('for-sale.index');

// PUBLIC: Books
Route::get('/books', [PublicBookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [PublicBookController::class, 'show'])->name('books.show');

// PUBLIC: Items
Route::get('/items', [PublicItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [PublicItemController::class, 'show'])->name('items.show');

Auth::routes();

Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }

    return view('auth.login');
})->middleware('guest')->name('login');

Route::get('/{section}', [SectionController::class, 'index'])
    ->whereIn('section', array_keys(config('collector.enabled_sections')))
    ->name('sections.index');

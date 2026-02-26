<?php
// routes/admin.php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    MediaFileController,
    BookController,
    ItemController,
    BanknoteController,
    CoinController,
    MagazineController,
    NewspaperController,
    PostcardController,
    StampController,
    DashboardController,
    UserController,
};

use App\Http\Controllers\Admin\Ajax\LookupController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('books', BookController::class);
Route::resource('items', ItemController::class);

// Polymorphic media (books/items)
Route::post('{type}/{id}/media', [MediaFileController::class, 'store'])
    ->whereIn('type', ['books', 'items'])
    ->name('media.store');

Route::delete('{type}/media/{file}', [MediaFileController::class, 'destroy'])
    ->whereIn('type', ['books', 'items'])
    ->name('media.destroy');

Route::patch('{type}/media/{file}/main', [MediaFileController::class, 'makeMain'])
    ->whereIn('type', ['books', 'items'])
    ->name('media.main');

Route::resource('newspapers', NewspaperController::class);
Route::resource('magazines', MagazineController::class);
Route::resource('banknotes', BanknoteController::class);
Route::resource('coins', CoinController::class);
Route::resource('postcards', PostcardController::class);
Route::resource('stamps', StampController::class);

Route::resource('profile', UserController::class);

Route::post('lookups/{type}', [LookupController::class, 'store'])
    ->name('lookups.ajax.store');

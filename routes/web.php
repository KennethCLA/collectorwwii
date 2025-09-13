<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BanknoteController;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\NewspaperController;
use App\Http\Controllers\PostcardController;
use App\Http\Controllers\StampController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;

use App\Http\Controllers\Ajax\BookTopicController;
use App\Http\Controllers\Ajax\BookSeriesController;
use App\Http\Controllers\Ajax\BookCoverController;

use App\Models\BookTopic;
use App\Models\BookCover;
use App\Models\BookSeries;
use App\Models\Location;
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

Route::resource('books', BookController::class);
Route::resource('items', ItemController::class);
Route::resource('newspapers', NewspaperController::class);
Route::resource('magazines', MagazineController::class);
Route::resource('banknotes', BanknoteController::class);
Route::resource('coins', CoinController::class);
Route::resource('postcards', PostcardController::class);
Route::resource('stamps', StampController::class);

Route::resource('profile', UserController::class);

Auth::routes();

Route::get('/login', function () {
    session(['url.intended' => url()->previous()]); // Bewaar de vorige pagina
    return view('auth.login');
})->name('login');

Route::post('/add-location', function (Request $request) {
    $location = Location::create(['name' => $request->name]);
    return response()->json(['success' => true, 'id' => $location->id, 'name' => $location->name]);
});

Route::middleware('auth')->group(function () {
    Route::post('/topics/ajax/store', [BookTopicController::class, 'storeAjax'])->name('topics.ajax.store');
    Route::post('/series/ajax/store', [BookSeriesController::class, 'storeAjax'])->name('series.ajax.store');
    Route::post('/covers/ajax/store', [BookCoverController::class, 'storeAjax'])->name('covers.ajax.store');
});

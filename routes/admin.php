<?php
// routes/admin.php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    MediaFileController,
    BookController,
    ItemController,
    BlogController,
    LookupIndexController,
    MapLocationController,
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
Route::resource('map-locations', MapLocationController::class)->except('show');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::post('blog', [BlogController::class, 'store'])->name('blog.store');
Route::get('blog/create', [BlogController::class, 'create'])->name('blog.create');
Route::get('blog/{id}/edit', [BlogController::class, 'edit'])->name('blog.edit');
Route::put('blog/{id}/edit', [BlogController::class, 'update'])->name('blog.update');
Route::delete('blog/{id}', [BlogController::class, 'destroy'])->name('blog.destroy');

// Polymorphic media
Route::post('{type}/{id}/media', [MediaFileController::class, 'store'])
    ->whereIn('type', ['books', 'items', 'banknotes', 'coins', 'magazines', 'newspapers', 'postcards', 'stamps', 'map-locations'])
    ->name('media.store');

Route::delete('{type}/media/{file}', [MediaFileController::class, 'destroy'])
    ->whereIn('type', ['books', 'items', 'banknotes', 'coins', 'magazines', 'newspapers', 'postcards', 'stamps', 'map-locations'])
    ->name('media.destroy');

Route::patch('{type}/media/{file}/main', [MediaFileController::class, 'makeMain'])
    ->whereIn('type', ['books', 'items', 'banknotes', 'coins', 'magazines', 'newspapers', 'postcards', 'stamps', 'map-locations'])
    ->name('media.main');

Route::resource('newspapers', NewspaperController::class);
Route::resource('magazines', MagazineController::class);
Route::resource('banknotes', BanknoteController::class);
Route::resource('coins', CoinController::class);
Route::resource('postcards', PostcardController::class);
Route::resource('stamps', StampController::class);

Route::resource('profile', UserController::class);

Route::get('lookups/{type}', [LookupIndexController::class, 'index'])
    ->whereIn('type', [
        'book-topics', 'book-covers', 'book-series', 'origins', 'locations',
        'item-categories', 'item-nationalities', 'item-organizations',
        'countries', 'currencies', 'nominal-values',
        'banknote-series', 'banknote-time-periods', 'banknote-designers', 'banknote-watermarks',
        'heads-of-state', 'colours', 'print-types',
        'coin-shapes', 'coin-materials', 'coin-occasions', 'coin-designers',
        'coin-strike-marks', 'coin-front-images', 'coin-front-texts',
        'coin-reverse-images', 'coin-reverse-texts', 'coin-rims', 'coin-rim-texts',
        'postcard-types', 'postcard-valuation-images',
        'stamp-types', 'stamp-designers', 'stamp-watermarks', 'stamp-gums',
        'stamp-perforations', 'stamp-printing-houses',
    ])
    ->name('lookups.index');
Route::post('lookups/{type}', [LookupIndexController::class, 'store'])
    ->whereIn('type', [
        'book-topics', 'book-covers', 'book-series', 'origins', 'locations',
        'item-categories', 'item-nationalities', 'item-organizations',
        'countries', 'currencies', 'nominal-values',
        'banknote-series', 'banknote-time-periods', 'banknote-designers', 'banknote-watermarks',
        'heads-of-state', 'colours', 'print-types',
        'coin-shapes', 'coin-materials', 'coin-occasions', 'coin-designers',
        'coin-strike-marks', 'coin-front-images', 'coin-front-texts',
        'coin-reverse-images', 'coin-reverse-texts', 'coin-rims', 'coin-rim-texts',
        'postcard-types', 'postcard-valuation-images',
        'stamp-types', 'stamp-designers', 'stamp-watermarks', 'stamp-gums',
        'stamp-perforations', 'stamp-printing-houses',
    ])
    ->name('lookups.store');
Route::delete('lookups/{type}/{id}', [LookupIndexController::class, 'destroy'])
    ->whereIn('type', [
        'book-topics', 'book-covers', 'book-series', 'origins', 'locations',
        'item-categories', 'item-nationalities', 'item-organizations',
        'countries', 'currencies', 'nominal-values',
        'banknote-series', 'banknote-time-periods', 'banknote-designers', 'banknote-watermarks',
        'heads-of-state', 'colours', 'print-types',
        'coin-shapes', 'coin-materials', 'coin-occasions', 'coin-designers',
        'coin-strike-marks', 'coin-front-images', 'coin-front-texts',
        'coin-reverse-images', 'coin-reverse-texts', 'coin-rims', 'coin-rim-texts',
        'postcard-types', 'postcard-valuation-images',
        'stamp-types', 'stamp-designers', 'stamp-watermarks', 'stamp-gums',
        'stamp-perforations', 'stamp-printing-houses',
    ])
    ->name('lookups.destroy');

Route::post('lookups/ajax/{type}', [LookupController::class, 'store'])
    ->name('lookups.ajax.store');

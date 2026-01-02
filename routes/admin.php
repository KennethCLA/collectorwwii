<?php
// routes/admin.php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    MediaFileController,
    BookController,
    BookFileController,
    ItemController,
    ItemFileController,
    BanknoteController,
    CoinController,
    MagazineController,
    NewspaperController,
    PostcardController,
    StampController,
    DashboardController,
    UserController
};

use App\Http\Controllers\Admin\Ajax\{
    BookTopicController,
    BookSeriesController,
    BookCoverController
};

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

// Book files (images / pdfs)
#Route::post('books/{book}/files', [BookFileController::class, 'store'])
#    ->name('books.files.store');

#Route::delete('books/files/{file}', [BookFileController::class, 'destroy'])
#    ->name('books.files.destroy');

#Route::patch('books/files/{file}/main', [BookFileController::class, 'makeMain'])
#    ->name('books.files.makeMain');

#Route::post('items/{item}/files', [ItemFileController::class, 'store'])
#    ->name('items.files.store');

#Route::delete('items/files/{file}', [ItemFileController::class, 'destroy'])
#    ->name('items.files.destroy');

#Route::patch('items/files/{file}/main', [ItemFileController::class, 'makeMain'])
#    ->name('items.files.makeMain');

#Route::post('books/{book}/media', [MediaFileController::class, 'storeForBook'])
#    ->name('books.media.store');

#Route::post('items/{item}/media', [MediaFileController::class, 'storeForItem'])
#    ->name('items.media.store');

#Route::delete('media/{file}', [MediaFileController::class, 'destroy'])
#    ->name('media.destroy');

#Route::patch('media/{file}/main', [MediaFileController::class, 'makeMain'])
#    ->name('media.makeMain');


Route::resource('newspapers', NewspaperController::class);
Route::resource('magazines', MagazineController::class);
Route::resource('banknotes', BanknoteController::class);
Route::resource('coins', CoinController::class);
Route::resource('postcards', PostcardController::class);
Route::resource('stamps', StampController::class);

Route::resource('profile', UserController::class);

Route::post('topics/ajax/store', [BookTopicController::class, 'storeAjax'])->name('topics.ajax.store');
Route::post('series/ajax/store', [BookSeriesController::class, 'storeAjax'])->name('series.ajax.store');
Route::post('covers/ajax/store', [BookCoverController::class, 'storeAjax'])->name('covers.ajax.store');

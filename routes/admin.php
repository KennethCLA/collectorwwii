<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    BookController,
    ItemController,
    BanknoteController,
    CoinController,
    MagazineController,
    NewspaperController,
    PostcardController,
    StampController,
    UserController
};

use App\Http\Controllers\Admin\Ajax\{
    BookTopicController,
    BookSeriesController,
    BookCoverController
};

Route::middleware(['auth'])->group(function () {

    Route::resource('books', BookController::class);
    Route::resource('items', ItemController::class);
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
});

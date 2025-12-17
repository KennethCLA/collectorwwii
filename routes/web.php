<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\BlogController;

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BanknoteController;
use App\Http\Controllers\Admin\CoinController;
use App\Http\Controllers\Admin\MagazineController;
use App\Http\Controllers\Admin\NewspaperController;
use App\Http\Controllers\Admin\PostcardController;
use App\Http\Controllers\Admin\StampController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ItemController;

use App\Http\Controllers\Admin\Ajax\BookTopicController;
use App\Http\Controllers\Admin\Ajax\BookSeriesController;
use App\Http\Controllers\Admin\Ajax\BookCoverController;

use App\Models\BookTopic;
use App\Models\BookCover;
use App\Models\BookSeries;
use App\Models\Location;
use Illuminate\Http\Request;
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

Auth::routes();

Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }

    return view('auth.login');
})->middleware('guest')->name('login');


Route::post('/add-location', function (Request $request) {
    $location = Location::create(['name' => $request->name]);
    return response()->json(['success' => true, 'id' => $location->id, 'name' => $location->name]);
});

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;

Route::post('/store', [NewsController::class, 'store'])->name('store');
Route::get('/all-news', [NewsController::class, 'index'])->name('index');
Route::get('/fetch-news', [NewsController::class, 'fetchNews'])->name('fetchNews');
Route::get('/article/{id}/details', [NewsController::class, 'showDetails'])->name('details');
Route::get('/compare', [NewsController::class, 'showComparisonForm'])->name('compare.form');
Route::post('/compare/saved-articles', [NewsController::class, 'compareSavedArticles'])->name('compare.saved.articles');
Route::post('/compare/texts', [NewsController::class, 'compareTexts'])->name('compare.texts');

Route::middleware('auth')->group(function () {
    Route::post('article/{id}/like', [NewsController::class, 'like'])->name('like');
    Route::post('article/{id}/bookmark', [NewsController::class, 'bookmark'])->name('bookmark');
    Route::get('/user/likes', [ProfileController::class, 'showLikes'])->name('profile.likes');
    Route::get('/user/bookmarks', [ProfileController::class, 'showBookmarks'])->name('profile.bookmarks');
});

Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

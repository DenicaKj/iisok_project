<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;

Route::post('/store', [NewsController::class, 'store'])->name('store');
Route::get('/all-news', [NewsController::class, 'index'])->name('index');
Route::get('/fetch-news', [NewsController::class, 'fetchNews'])->name('fetchNews');
Route::get('/article/{id}/details', [NewsController::class, 'showDetails'])->name('details');
Route::get('/compare', [NewsController::class, 'showComparisonForm'])->name('compare.form');
Route::post('/compare/saved-articles', [NewsController::class, 'compareSavedArticles'])->name('compare.saved.articles');
Route::post('/compare/texts', [NewsController::class, 'compareTexts'])->name('compare.texts');



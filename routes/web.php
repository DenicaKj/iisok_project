<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;

Route::post('/store', [NewsController::class, 'store'])->name('store');
Route::get('/all-news', [NewsController::class, 'index']);
Route::get('/fetch-news', [NewsController::class, 'fetchNews']);


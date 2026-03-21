<?php

use App\Http\Controllers\SouvenirController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Home page with recommendation form
Route::get('/', [HomeController::class, 'index'])->name('home');

// Catalog page
Route::get('/catalog', [HomeController::class, 'catalog'])->name('catalog');

// Recommendation submission
Route::post('/recommend', [HomeController::class, 'submitRecommendation'])->name('recommend.submit');

// Recommendation results
Route::get('/recommend/results', [HomeController::class, 'showResults'])->name('recommend.results');

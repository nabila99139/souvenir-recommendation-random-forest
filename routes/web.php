<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SouvenirController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/verify', [AuthController::class, 'showVerify'])->name('auth.verify');
Route::post('/verify', [AuthController::class, 'verify'])->name('auth.verify');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Home page with recommendation form
Route::get('/', [HomeController::class, 'index'])->name('home');

// Catalog page
Route::get('/catalog', [HomeController::class, 'catalog'])->name('catalog');

// Recommendation submission
Route::post('/recommend', [HomeController::class, 'submitRecommendation'])->name('recommend.submit');

// Recommendation results
Route::get('/recommend/results', [HomeController::class, 'showResults'])->name('recommend.results');

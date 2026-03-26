<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SouvenirManagementController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SouvenirController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

// Welcome/Landing page
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/verify', [AuthController::class, 'showVerify'])->name('auth.verify');
Route::post('/verify', [AuthController::class, 'verify'])->name('auth.verify');
Route::post('/resend', [AuthController::class, 'resend'])->name('auth.resend');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Development: Clear rate limits (REMOVE IN PRODUCTION)
Route::post('/clear-rate-limit', [AuthController::class, 'clearRateLimit'])->name('clear.rate.limit');

// ============================================
// AUTHENTICATED USER ROUTES
// ============================================

Route::middleware('auth.session')->group(function () {
    // Home page with recommendation form
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Catalog page
    Route::get('/home/catalog', [HomeController::class, 'catalog'])->name('catalog');

    // Recommendation submission
    Route::post('/home/recommend', [HomeController::class, 'submitRecommendation'])->name('recommend.submit');

    // Recommendation results
    Route::get('/home/recommend/results', [HomeController::class, 'showResults'])->name('recommend.results');
});

// ============================================
// ADMIN ROUTES (Root Admin Access Only)
// ============================================

Route::middleware(['auth.session', 'admin.only'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.index');

    // System Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');

    // System Information
    Route::get('/system', [AdminController::class, 'system'])->name('system');

    // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/promote', [UserManagementController::class, 'promoteToAdmin'])->name('promote');
        Route::post('/{user}/demote', [UserManagementController::class, 'demoteFromAdmin'])->name('demote');
    });

    // Souvenir Management Routes
    Route::prefix('souvenirs')->name('souvenirs.')->group(function () {
        Route::get('/', [SouvenirManagementController::class, 'index'])->name('index');
        Route::get('/create', [SouvenirManagementController::class, 'create'])->name('create');
        Route::post('/', [SouvenirManagementController::class, 'store'])->name('store');
        Route::get('/{souvenir}', [SouvenirManagementController::class, 'show'])->name('show');
        Route::get('/{souvenir}/edit', [SouvenirManagementController::class, 'edit'])->name('edit');
        Route::put('/{souvenir}', [SouvenirManagementController::class, 'update'])->name('update');
        Route::delete('/{souvenir}', [SouvenirManagementController::class, 'destroy'])->name('destroy');
    });

    // System Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/general', [SystemSettingsController::class, 'index'])->name('general');
        Route::put('/general', [SystemSettingsController::class, 'update'])->name('update');
        Route::get('/info', [SystemSettingsController::class, 'info'])->name('info');
    });
});

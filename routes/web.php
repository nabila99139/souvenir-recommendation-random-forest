<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SouvenirManagementController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SouvenirController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SellerDashboardController;
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
// SELLER ROUTES (Seller Access Only)
// ============================================

Route::middleware(['auth.session', 'seller.only'])->prefix('seller')->name('seller.')->group(function () {
    // Seller Dashboard
    Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');

    // Business Profile Management
    Route::get('/business-profile', [SellerDashboardController::class, 'businessProfile'])->name('business-profile');
    Route::put('/business-profile', [SellerDashboardController::class, 'updateBusinessProfile'])->name('update-business-profile');

    // Souvenir Catalog Management
    Route::prefix('souvenirs')->group(function () {
        Route::get('/', [SellerDashboardController::class, 'souvenirs'])->name('souvenirs');
        Route::get('/create', [SellerDashboardController::class, 'createSouvenir'])->name('create-souvenir');
        Route::post('/', [SellerDashboardController::class, 'storeSouvenir'])->name('store-souvenir');
        Route::get('/{id}/edit', [SellerDashboardController::class, 'editSouvenir'])->name('edit-souvenir');
        Route::put('/{id}', [SellerDashboardController::class, 'updateSouvenir'])->name('update-souvenir');
        Route::delete('/{id}', [SellerDashboardController::class, 'deleteSouvenir'])->name('delete-souvenir');
    });

    // Lead Tracking
    Route::get('/leads', [SellerDashboardController::class, 'leads'])->name('leads');
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
        Route::get('/create', [AdminController::class, 'createUser'])->name('create');
        Route::post('/', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');

        // Role management routes (Root only)
        Route::post('/{user}/promote-root', [UserManagementController::class, 'promoteToAdmin'])->name('promote-root');
        Route::post('/{user}/change-customer', [UserManagementController::class, 'changeToCustomer'])->name('change-customer');
        Route::post('/{user}/change-seller', [UserManagementController::class, 'changeToSeller'])->name('change-seller');
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

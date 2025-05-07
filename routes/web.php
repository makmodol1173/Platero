<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (provided by Laravel Breeze)
require __DIR__.'/auth.php';

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
});

// Restaurant routes
Route::resource('restaurants', RestaurantController::class);

// Menu routes
Route::resource('restaurants.menus', MenuController::class);

// Menu Item routes
Route::resource('restaurants.menus.menu-items', MenuItemController::class)->except(['index', 'show']);

// Order routes
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/restaurants/{restaurant}/orders/create', [OrderController::class, 'create'])->name('restaurants.orders.create');
    Route::post('/restaurants/{restaurant}/orders', [OrderController::class, 'store'])->name('restaurants.orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
});

// Payment routes
Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/payment', [PaymentController::class, 'showPaymentPage'])->name('orders.payment');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'initiatePayment'])->name('orders.initiate-payment');
    Route::get('/payments/success', [PaymentController::class, 'handleSuccess'])->name('payments.success');
    Route::get('/payments/fail', [PaymentController::class, 'handleFailure'])->name('payments.fail');
    Route::get('/payments/cancel', [PaymentController::class, 'handleCancel'])->name('payments.cancel');
    Route::post('/payments/ipn', [PaymentController::class, 'handleIPN'])->name('payments.ipn');
});

// Order tracking routes
Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/track', [OrderTrackingController::class, 'track'])->name('orders.track');
    Route::get('/orders/{order}/track/location', [OrderTrackingController::class, 'getLocation'])->name('orders.track.location');
    Route::post('/orders/{order}/track/location', [OrderTrackingController::class, 'updateLocation'])->name('orders.track.update-location');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Restaurant management
    Route::get('/restaurants', [AdminController::class, 'restaurants'])->name('admin.restaurants');
    
    // Order management
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    
    // System settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::patch('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});
<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PasswordOtpController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Routes for Authenticated Users (All Roles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Table Reservation (Customer or Authenticated User)
    Route::post('/tables/reserve', [TableController::class, 'reserve'])->name('tables.reserve');
    Route::get('/tables/listOfTables', [TableController::class, 'listOfTables'])->name('tables.listOfTables');

    // Online Ordering
    Route::post('/orderOnline', [OrderController::class, 'orderOnline'])->name('orders.orderOnline');
});

/*
|--------------------------------------------------------------------------
| Admin & Cashier Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|cashier'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/generateReport', [DashboardController::class, 'generateReport'])->name('dashboard.generateReport');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/list', [UserController::class, 'list'])->name('users.list');
    Route::get('/users/{user}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/list', [RoleController::class, 'list'])->name('roles.list');
    Route::get('/roles/{role}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/list', [PermissionController::class, 'list'])->name('permissions.list');
    Route::get('/permissions/{permission}', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    // POS
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/checkout', [POSController::class, 'create'])->name('pos.checkout');
    Route::get('/order/update/{order_id}', [POSController::class, 'update'])->name('pos.update');

    // Tables
    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
    Route::get('/tables/list', [TableController::class, 'list'])->name('tables.list');
    Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
    Route::get('/tables/{table}', [TableController::class, 'edit'])->name('tables.edit');
    Route::post('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/list', [OrderController::class, 'list'])->name('orders.list');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'edit'])->name('orders.edit');
    Route::post('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin & Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|customer'])->group(function () {

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/list', [ProductController::class, 'list'])->name('products.list');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Customer Home Page
    Route::get('/', function () {
        return Inertia::render('customers/index');
    })->name('customers.index');
    Route::get('/orders/customer/history', function () {
        return Inertia::render('customers/history');
    })->name('customers.history');
    Route::get('/my-profile', function () {
        return Inertia::render('auth/user/UserDetails');
    })->name('user.profile');
    Route::get('/orders/user/history', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/products/user/menu', [ProductController::class, 'menu'])->name('products.menu');
    
});

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::post('/password/send-otp', [PasswordOtpController::class, 'sendOtp']);
Route::post('/password/verify-otp', [PasswordOtpController::class, 'verifyOtp']);
/*
|--------------------------------------------------------------------------
| External Route Files
|--------------------------------------------------------------------------
*/
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

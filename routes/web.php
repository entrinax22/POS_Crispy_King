<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PermissionController;

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

Route::middleware(['auth', 'role:admin|cashier'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    // Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/list', [UserController::class, 'list'])->name('users.list');
    Route::get('/users/{user}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::post('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('/roles/list', [RoleController::class, 'list'])->name('roles.list');
    Route::get('/roles/{role}', [RoleController::class, 'edit'])->name('roles.edit');

    Route::get('/permissions/list', [PermissionController::class, 'list'])->name('permissions.list');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{permission}', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::post('/checkout', [POSController::class, 'create'])->name('pos.checkout');
    Route::get('/order/update/{order_id}', [POSController::class, 'update'])->name('pos.update');

    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
    Route::get('/tables/list', [TableController::class, 'list'])->name('tables.list');
    Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
    Route::get('/tables/{table}', [TableController::class, 'edit'])->name('tables.edit');
    Route::post('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
});

Route::middleware(['auth', 'role:admin|customer'])->group(function () {
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/list', [ProductController::class, 'list'])->name('products.list');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/', function () {
        return Inertia::render('customers/index');
    })->name('customers.index');

});
Route::middleware(['auth'])->group(function () {
    Route::get('/tables/reserve', [TableController::class, 'reserve'])->name('tables.reserve');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

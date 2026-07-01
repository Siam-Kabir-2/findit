<?php

use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminClaimController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [UserAuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/items', [ItemController::class, 'index'])->name('items.index');

Route::middleware('auth.user')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/my-items', [ItemController::class, 'myItems'])->name('items.mine');
    Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy')->whereNumber('id');

    Route::post('/items/{id}/claims', [ClaimController::class, 'store'])->name('claims.store')->whereNumber('id');
    Route::get('/my-claims', [ClaimController::class, 'myClaims'])->name('claims.mine');
});

Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show')->whereNumber('id');

Route::prefix('admin')->middleware('auth.admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/claims', [AdminClaimController::class, 'index'])->name('admin.claims.index');
    Route::post('/claims/{id}/approve', [AdminClaimController::class, 'approve'])->name('admin.claims.approve')->whereNumber('id');
    Route::post('/claims/{id}/reject', [AdminClaimController::class, 'reject'])->name('admin.claims.reject')->whereNumber('id');

    Route::get('/items', [AdminItemController::class, 'index'])->name('admin.items.index');
    Route::post('/items/{id}/status', [AdminItemController::class, 'updateStatus'])->name('admin.items.status')->whereNumber('id');
    Route::delete('/items/{id}', [AdminItemController::class, 'destroy'])->name('admin.items.destroy')->whereNumber('id');

    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy')->whereNumber('id');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy')->whereNumber('id');

    Route::get('/locations', [AdminLocationController::class, 'index'])->name('admin.locations.index');
    Route::post('/locations', [AdminLocationController::class, 'store'])->name('admin.locations.store');
    Route::delete('/locations/{id}', [AdminLocationController::class, 'destroy'])->name('admin.locations.destroy')->whereNumber('id');

    Route::get('/audit', [AdminAuditController::class, 'index'])->name('admin.audit.index');
});

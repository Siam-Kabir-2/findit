<?php

use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminClaimController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show')->whereNumber('item');

Route::post('/preferences/board-view', [PreferenceController::class, 'updateBoardView'])->name('preferences.board-view');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/my-items', [ItemController::class, 'myItems'])->name('items.mine');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit')->whereNumber('item');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update')->whereNumber('item');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy')->whereNumber('item');

    Route::post('/items/{item}/claims', [ClaimController::class, 'store'])->name('claims.store')->whereNumber('item');
    Route::get('/my-claims', [ClaimController::class, 'myClaims'])->name('claims.mine');
});

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

require __DIR__.'/auth.php';

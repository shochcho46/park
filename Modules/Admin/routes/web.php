<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\CategoryController;
use Modules\Admin\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::prefix('admin')->group(function () {
    Route::middleware(['auth:admin'])->group(function () {
        Route::resource('category', CategoryController::class)->names([
            'index' => 'admin.category.index',
            'create' => 'admin.category.create',
            'store' => 'admin.category.store',
            'show' => 'admin.category.show',
            'edit' => 'admin.category.edit',
            'update' => 'admin.category.update',
            'destroy' => 'admin.category.destroy',
        ]);

        Route::resource('account', AccountController::class)->names([
            'index' => 'admin.account.index',
            'create' => 'admin.account.create',
            'store' => 'admin.account.store',
            'show' => 'admin.account.show',
            'edit' => 'admin.account.edit',
            'update' => 'admin.account.update',
            'destroy' => 'admin.account.destroy',
        ]);

        // Export route for accounts
        Route::get('accountdetail/export', [AccountController::class, 'export'])->name('admin.account.export');
    });
});

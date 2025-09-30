<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\Http\Controllers\RoleController;

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

Route::prefix('admin/role/permission')->group(function () {

   Route::middleware(['auth:admin'])->group(function () {

       Route::controller(RoleController::class)->group(function () {
           Route::get('role/index', 'roleIndex')->name('admin.roleIndex');
           Route::get('role/create', 'roleCreate')->name('admin.roleCreate');
           Route::post('role/store', 'roleStore')->name('admin.roleStore');
           Route::get('role/{role}/edit', 'roleEdit')->name('admin.roleEdit');
           Route::put('role/{role}/update', 'roleUpdate')->name('admin.roleUpdate');
           Route::delete('role/{role}/delete', 'roleDestroy')->name('admin.roleDestroy');
           Route::get('role/{role}/with/permission', 'roleWithPermission')->name('admin.roleWithPermission');
           Route::put('role/{role}/with/permission/assign', 'roleWithPermissionStore')->name('admin.roleWithPermissionStore');



           Route::get('permission/index', 'permissionIndex')->name('admin.permissionIndex');
           Route::get('permission/create', 'permissionCreate')->name('admin.permissionCreate');
           Route::post('permission/store', 'permissionStore')->name('admin.permissionStore');
           Route::get('permission/{permission}/edit', 'permissionEdit')->name('admin.permissionEdit');
           Route::put('permission/{permission}/update', 'permissionUpdate')->name('admin.permissionUpdate');
           Route::delete('permission/{permission}/delete', 'permissionDestroy')->name('admin.permissionDestroy');

       });

   });

});

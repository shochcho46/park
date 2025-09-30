<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\UserAuthController;
use Modules\User\Http\Controllers\UserController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('user', UserController::class)->names('user');
});



Route::prefix('v1')->group(function () {

    Route::prefix('user')->group(function () {

        Route::controller(UserAuthController::class)->group(function () {
            Route::post('login', 'userLoginValidation');

        });

        Route::middleware(['auth:api'])->group(function () {

            Route::controller(UserAuthController::class)->group(function () {
                Route::get('auth/logout', 'userLogout');
                Route::get('full/detail', 'userDetail');
            });



        });


    });

});

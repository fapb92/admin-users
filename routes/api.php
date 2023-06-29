<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('signup', [RegisterController::class, 'store']);
Route::get('verification/email/{vcode}/{hash}', [VerifyEmailController::class, 'verify']);
Route::get('verification/email/resend', [VerifyEmailController::class, 'resend_email'])->middleware('auth:api');

Route::post('login', [LoginController::class, 'login']);

Route::post('password/reset/send', [ResetPasswordController::class, 'sentEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'reset'])->name('password.reset');

Route::get('token/refresh', [TokenController::class, 'refresh'])->middleware('token.refresh');

Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::get('logout', [TokenController::class, 'logout']);
    Route::group([
        'prefix' => 'user',
        'controller' => UserController::class,
    ], function () {
        Route::get('/', 'index');
        Route::put('/', 'update');
        Route::get('roles', 'show_roles');
        Route::post('role/select', 'select_role');
    });

    Route::group([
        'prefix' => 'role',
        'controller' => RoleController::class,
    ], function () {
        Route::get('list', 'index')->middleware('able_to:view_all_roles');
        Route::get('{role}', 'show')->middleware('able_to:view_rol_details');
        Route::post('assign/user/{user}', 'assign')->middleware('able_to:assign_roles');
        Route::post('remove/user/{user}', 'remove')->middleware('able_to:remove_roles');
    });

    Route::get('permissions', [PermissionController::class, 'index'])->middleware('able_to:view_all_permissions');

    Route::group([
        'prefix' => 'users',
        'controller' => AdminUserController::class
    ], function () {
        Route::get('/', 'index')->middleware('able_to:view_all_user');
        Route::get('/{user}', 'show')->middleware('able_to:view_user_details');
        Route::post('create', 'store')->middleware('able_to:create_users');
        Route::put('/{user}', 'update')->middleware('able_to:update_users');
        Route::delete('/{user}', 'destroy')->middleware('able_to:erase_users');
    });
});

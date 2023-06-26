<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerifyEmailController;
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
    Route::get('user', [UserController::class, 'index']);
    Route::get('logput', [TokenController::class, 'logout']);
});

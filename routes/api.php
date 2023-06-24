<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
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

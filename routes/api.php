<?php

use App\Http\Controllers\V1\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-new-device', [AuthController::class, 'verifyNewDevice']);
    Route::post('verify-remove-device', [AuthController::class, 'verifyRemoveDevice']);
    Route::get('active-devices', [AuthController::class, 'getListActiveDevice']);
});

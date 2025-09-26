<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {

    // Register & OTP
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:3,10');

    // Login & Logout
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Change Password (must be logged in)
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

    // Reset Password
    Route::post('send-reset-code', [AuthController::class, 'sendResetCode']);
    Route::post('update-password', [AuthController::class, 'updatePassword']);

    // Social Login (Google, Facebook, Apple)
    Route::post('social-login/{provider}', [AuthController::class, 'socialLogin']);
});

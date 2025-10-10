<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\MobileWalletController;
use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\NotificationController;

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
Route::middleware('auth:sanctum')->group(function () {

    Route::put('/settings/password', [PasswordController::class, 'update']);

      Route::apiResource('cards', CardController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
        Route::apiResource('mobile-wallets', MobileWalletController::class);

});
Route::get('/faqs', [FaqController::class, 'index']);
Route::post('/faqs', [FaqController::class, 'store']);
Route::put('/faqs/{id}', [FaqController::class, 'update']);
Route::delete('/faqs/{id}', [FaqController::class, 'destroy']);

Route::get('/pages/privacy-policy', [PageController::class, 'privacyPolicy']);
Route::get('/pages/terms-and-conditions', [PageController::class, 'termsAndConditions']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notification-setting', [UserController::class, 'getNotificationSetting']);
    Route::put('/notification-setting', [UserController::class, 'updateNotificationSetting']);
       Route::get('/profile', [UserController::class, 'getProfile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::delete('/profile',[UserController::class,'deleteProfile']);
});

Route::prefix('reviews')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ReviewController::class, 'index']);
    Route::get('show/{review}', [ReviewController::class, 'show']);
    Route::post('doctors/{doctor}/reviews', [ReviewController::class, 'store']);
    Route::put('edit/{review}', [ReviewController::class, 'update']);
    Route::delete('delete/{review}', [ReviewController::class, 'destroy']);
    Route::post('verify/{review}/verify', [ReviewController::class, 'verify']);
});

Route::prefix('favourites')->group(function () {
    Route::get('/', [FavouriteController::class, 'index']);
    Route::post('doctor/{doctor}', [FavouriteController::class, 'store']);
    Route::delete('doctor/{doctor}', [FavouriteController::class, 'destroy']);
});

Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});

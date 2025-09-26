<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\MobileWalletController;
use App\Http\Controllers\Api\UserController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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

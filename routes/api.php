<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignLeadController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PaymentCallbackController;
use App\Http\Controllers\Api\PaymentGatewayController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/campaign-leads', [CampaignLeadController::class, 'store']);

Route::get('/pages/{slug}', [PageController::class, 'show']);

Route::get('/payment-gateways', [PaymentGatewayController::class, 'index']);

Route::get('/payment-callback/success', [PaymentCallbackController::class, 'success'])
    ->name('payment.callback.success');
Route::get('/payment-callback/cancel', [PaymentCallbackController::class, 'cancel'])
    ->name('payment.callback.cancel');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('throttle:3,5');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])
        ->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/user/orders', [OrderController::class, 'index']);
    Route::get('/user/enquiries', [UserController::class, 'enquiries']);
    Route::post('/coupons/validate', [CouponController::class, 'validate']);
});

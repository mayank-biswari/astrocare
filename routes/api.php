<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignLeadController;
use Illuminate\Support\Facades\Route;

Route::post('/campaign-leads', [CampaignLeadController::class, 'store']);

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

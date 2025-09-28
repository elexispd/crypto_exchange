<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CryptoApiController;
use App\Http\Controllers\Api\V1\KycDocumentController;
use App\Http\Controllers\Api\V1\WalletController;

Route::prefix('v1')->group(function () {
    // Public
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/verify-secret-phrase', [AuthController::class, 'verifySecretPhrase']);
        Route::post('/verify-pin', [AuthController::class, 'verifyPin']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('kyc', KycDocumentController::class)->only(['index', 'store', 'show']);
        Route::post('/kyc/{kyc}', [KycDocumentController::class, 'update'])->name('kyc.update');

        Route::get('/wallet/getSecretPhrase', [WalletController::class,  'getPhrase'] );
        Route::apiResource('wallet', WalletController::class)->only(['store', 'show']);

        Route::get('/crypto/prices', [CryptoApiController::class, 'prices']);
        Route::get('/crypto/price/{id}', [CryptoApiController::class, 'price']);

    });
});

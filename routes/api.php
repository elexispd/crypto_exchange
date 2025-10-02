<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CryptoApiController;
use App\Http\Controllers\Api\V1\DepositController;
use App\Http\Controllers\Api\V1\KycDocumentController;
use App\Http\Controllers\Api\V1\TransactionController;
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

        Route::get('/wallet/getSecretPhrase', [WalletController::class,  'getPhrase']);
        Route::get('/wallet/getBalances', [WalletController::class,  'getBalances']);
        Route::apiResource('wallet', WalletController::class)->only(['store', 'show']);

        Route::get('/crypto/prices', [CryptoApiController::class, 'prices']);
        Route::get('/crypto/price/{id}', [CryptoApiController::class, 'price']);
        Route::get('/crypto/market', [CryptoApiController::class, 'market']);
        Route::get('/crypto/coins', [CryptoApiController::class, 'coins']);

        // Route::post('/deposits', [DepositController::class, 'store']);
        // Route::get('/deposits', [DepositController::class, 'index']);
        // Route::get('/deposits/{id}', [DepositController::class, 'show']);

        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions/deposit', [TransactionController::class, 'deposit']);
        Route::post('/transactions/withdraw', [TransactionController::class, 'withdraw']);
        Route::post('/transactions/swap', [TransactionController::class, 'swap']);
        Route::get('/transactions/deposits/{transaction}', [TransactionController::class, 'show']);
        Route::get('/transactions/withdrawals/{transaction}', [TransactionController::class, 'show']);
        Route::get('/transactions/swaps/{transaction}', [TransactionController::class, 'show']);


    });



});

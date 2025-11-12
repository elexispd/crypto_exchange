<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CardController;
use App\Http\Controllers\Api\V1\CryptoApiController;
use App\Http\Controllers\Api\V1\DepositController;
use App\Http\Controllers\Api\V1\InvestController;
use App\Http\Controllers\Api\V1\KycDocumentController;
use App\Http\Controllers\Api\V1\PortfolioController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\WithdrawController;
use App\Console\Commands\ClearOptimizationCommand;
use Illuminate\Support\Facades\Artisan;

Route::get('/test', [CryptoApiController::class, 'market']);

Route::prefix('v1')->group(function () {
    // Public
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::post('/request-reset', [AuthController::class, 'requestReset']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/verify-secret-phrase', [AuthController::class, 'verifySecretPhrase']);
        Route::post('/verify-pin', [AuthController::class, 'verifyPin']);
        Route::post('/create-pin', [AuthController::class, 'createPin']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/profile', [ProfileController::class, 'show']);

        Route::apiResource('kyc', KycDocumentController::class)->only(['index', 'store', 'show']);
        Route::post('/kyc/{kyc}', [KycDocumentController::class, 'update'])->name('kyc.update');

        Route::get('/wallet/getSecretPhrase', [WalletController::class,  'getPhrase']);
        Route::get('/wallet/getBalances', [WalletController::class,  'getBalances']);
        Route::get('/wallet/{wallet}/{network}', [WalletController::class,  'getWalletAddressBySymbol']);
        Route::apiResource('wallet', WalletController::class)->only(['store', 'show']);

        Route::get('/crypto/prices', [CryptoApiController::class, 'prices']);
        Route::get('/crypto/price/{id}', [CryptoApiController::class, 'price']);
        Route::get('/crypto/market', [CryptoApiController::class, 'market']);
        Route::get('/crypto/coins', [CryptoApiController::class, 'coins']);
        Route::get('/crypto/assets', [CryptoApiController::class, 'allAssets']);


        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions/deposit', [TransactionController::class, 'deposit']);
        // Route::post('/transactions/withdraw', [TransactionController::class, 'withdraw']);

        Route::post('/withdraw', [WithdrawController::class, 'store']);
        Route::get('/withdraw/info', [WithdrawController::class, 'withdrawInfo']);

        Route::post('/transactions/swap', [TransactionController::class, 'swap']);
        Route::get('/transactions/preview-swap', [TransactionController::class, 'previewSwap']);


        Route::get('/transactions/deposits/{transaction}', [TransactionController::class, 'show']);
        Route::get('/transactions/withdraws/{transaction}', [TransactionController::class, 'show']);
        Route::get('/transactions/swaps/{transaction}', [TransactionController::class, 'show']);


        Route::get('/cards', [CardController::class, 'show']);
        Route::post('/cards', [CardController::class, 'store']);
        Route::get('/cards/variations', [CardController::class, 'variations']);
        Route::put('/cards', [CardController::class, 'fundingSource']);
        Route::post('/cards/freeze', [CardController::class, 'freeze']);
        Route::post('/cards/unfreeze', [CardController::class, 'unfreeze']);
        Route::delete('/cards/{card_id}', [CardController::class, 'destroy']);

        Route::post('/investments', [InvestController::class, 'store']);
        Route::get('/investments', [InvestController::class, 'index']);
        Route::get('/investments/{invest}', [InvestController::class, 'show']);
        Route::post('/investments/{invest}/redeem', [InvestController::class, 'redeem']);

        Route::get('/investment-plans/{plan}', [InvestController::class, 'getInvestmentPlan']);
        Route::get('/investment-plans/network/{network}', [InvestController::class, 'getInvestmentPlans']);

        Route::get('/portfolio/stakes/{invest_id}', [PortfolioController::class, 'portfolioTransactions']);
    });

    Route::post('/artisan/optimize-clear', function (Request $request) {
        $token = $request->header('X-Artisan-Token');

        if (!$token || $token !== config('app.artisan_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Execute via command
            Artisan::call('app:clear-optimization', [
                '--token' => $token
            ]);

            $output = Artisan::output();

            logger()->info('Optimization cleared via API', [
                'ip' => $request->ip(),
                'output' => $output
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Optimization cleared successfully',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            logger()->error('Clear optimization failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Command failed',
                'error' => $e->getMessage()
            ], 500);
        }
    });
});

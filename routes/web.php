<?php

use App\Http\Controllers\AdminWalletController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentPlanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Support\Facades\Mail;









Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('/create', 'create')->name('users.create');
        Route::post('/store', 'store')->name('users.store');
        Route::get('/change-password', 'showChangePassword')->name('users.changePasswordForm');
        Route::post('/change-password', 'changePassword')->name('users.changePassword');
        Route::put('/{user}/status', 'changeStatus')->name('users.changeStatus');
        Route::get('/{user}', 'show')->name('users.show');
        Route::put('/{user}/update', 'update')->name('users.update');
        Route::delete('/{user}/delete', 'destroy')->name('users.destroy');


    });

    Route::controller(KycController::class)->prefix('kyc')->group(function () {
        Route::get('/', [KycController::class, 'index'])->name('admin.kyc.index');
        Route::put('/{kyc}/update', [KycController::class, 'update'])->name('admin.kyc.update');
        Route::delete('/{kyc}/delete', [KycController::class, 'destroy'])->name('admin.kyc.destroy');
    });

    Route::prefix('admin/wallet/')->controller(AdminWalletController::class)
    ->name('admin.walletmethod.')
    ->group(function () {
        Route::get('create', 'create')->name('create');
        Route::get('', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::put('{wallet}', 'updateStatus')->name('updateStatus');
    });

    Route::controller(WalletController::class)->prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('admin.wallet.index');
        Route::post('/store', [WalletController::class, 'store'])->name('admin.wallet.store');
        Route::get('/create', [WalletController::class, 'create'])->name('admin.wallet.create');
        Route::get('/{wallet}/delete', [WalletController::class, 'destroy'])->name('admin.wallet.destroy');
    });

    Route::controller(DepositController::class)->prefix('deposit')->group(function () {
        Route::get('/', [DepositController::class, 'index'])->name('admin.deposit.index');
        Route::put('/{deposit}', [DepositController::class, 'update'])->name('admin.deposit.update');
    });

    Route::controller(WithdrawController::class)->prefix('withdraw')->group(function () {
        Route::get('/', 'index')->name('admin.withdraw.index');
        Route::put('/{withdraw}', 'update')->name('admin.withdraw.update');
    });

    Route::controller(TransactionController::class)->prefix('transaction')->group(function () {
        Route::get('/', 'index')->name('admin.transaction.index');
        Route::get('/create-fee', 'fees')->name('admin.transaction.fees');
        Route::post('/create-fee', 'storeFee')->name('admin.transaction.storeFee');
    });

    Route::controller(InvestmentPlanController::class)->prefix('investment-plans')->group(function () {
        Route::get('/', 'index')->name('admin.plan.index');
        Route::get('/create', 'create')->name('admin.plan.create');
        Route::post('/create', 'store')->name('admin.plan.store');
        Route::post('/update', 'update')->name('admin.plan.update');
        Route::post('/change-status', 'changeStatus')->name('admin.plan.changeStatus');
    });

    Route::get('investments', [InvestmentController::class, 'index'])->name('admin.investment.index');





});

require __DIR__ . '/auth.php';

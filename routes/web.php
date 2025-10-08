<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\InvestmentController as AdminInvestmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WithdrawController as AdminWithdrawalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\UserController;
use App\Models\KycDocument;
use Illuminate\Support\Facades\Mail;



Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('/create', 'create')->name('users.create');
        Route::post('/store', 'store')->name('users.store');
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


});

require __DIR__ . '/auth.php';

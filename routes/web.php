<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\InvestmentController as AdminInvestmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WithdrawController as AdminWithdrawalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AdminKYCController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
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
});

require __DIR__ . '/auth.php';

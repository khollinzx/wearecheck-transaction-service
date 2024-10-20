<?php

use App\Http\Controllers\Auth\UserOnboardController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::get('welcome', [UserOnboardController::class, 'welcome']);
    Route::group(['middleware' => ['validate.headers']], function () {
        Route::group(['prefix' => 'onboard' ], function () {
            Route::post('login', [UserOnboardController::class, 'login']);
            Route::post('register', [UserOnboardController::class, 'register']);
        });
        Route::group(['middleware' => ['auth:api']], function () {
            Route::group(['prefix' => 'users'], function () {
                Route::get('wallet-balance', [TransactionController::class, 'getWalletBalance']);
                Route::post('make-payment', [TransactionController::class, 'makePayment']);
                Route::post('fund-wallet', [TransactionController::class, 'fundWallet']);
            });
        });
    });
});

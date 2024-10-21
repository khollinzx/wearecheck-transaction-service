<?php

use App\Http\Controllers\Auth\UserOnboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::get('welcome', [UserOnboardController::class, 'welcome']);
    Route::group(['middleware' => ['validate.headers']], function () {
        Route::group(['prefix' => 'onboard' ], function () {
            Route::post('login', [UserOnboardController::class, 'login']);
            Route::post('register', [UserOnboardController::class, 'register']);
        });
        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('balance', [TransactionController::class, 'getBalance']);
            Route::post('transaction', [TransactionController::class, 'performTransactions']);
        });
    });
});

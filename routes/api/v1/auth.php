<?php

use App\Http\Controllers\v1\AuthController;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/me', 'me')->name('me');
        Route::post('/login', 'login')->name('login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/register', 'register')->name('register');
            Route::post('/change-password', 'changePassword')->name('change-password');
            Route::post('/forgot-password', 'forgotPassword')->name('forgot-password');
            Route::post('/reset-password', 'resetPassword')->name('reset-password');
        });
    });

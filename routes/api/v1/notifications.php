<?php

use App\Http\Controllers\v1\NotificationController;
use Illuminate\Support\Facades\Route;

Route::controller(NotificationController::class)
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/list', 'list')
            ->name('list')
            ->middleware('auth:sanctum');
    });

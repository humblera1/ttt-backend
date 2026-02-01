<?php

use App\Http\Controllers\v1\NotificationController;
use Illuminate\Support\Facades\Route;

Route::controller(NotificationController::class)
    ->prefix('notifications')
    ->name('notifications.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/list', 'list')->name('list');
        Route::patch('/{notification}/read', 'markAsRead')->name('markAsRead');
    });

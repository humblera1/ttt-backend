<?php

use App\Http\Controllers\v1\CommentController;
use Illuminate\Support\Facades\Route;

Route::controller(CommentController::class)
    ->prefix('comments')
    ->name('comments.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::patch('/{comment}', 'update')->name('comments.update');
    });

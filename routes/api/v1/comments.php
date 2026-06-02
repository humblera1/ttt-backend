<?php

use App\Http\Controllers\v1\Comment\CommentVoteController;
use App\Http\Controllers\v1\CommentController;
use Illuminate\Support\Facades\Route;

Route::controller(CommentController::class)
    ->prefix('comments')
    ->name('comments.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::patch('/{comment}', 'update')->name('comments.update');
        Route::delete('/{comment}', 'delete')->name('comments.delete');
        Route::post('/{restorable_comment}/restore', 'restore')->name('comments.restore');

        Route::put('/{comment}/vote', [CommentVoteController::class, 'update'])
            ->name('vote.update');
    });

<?php

use App\Http\Controllers\v1\CommentController;
use App\Http\Controllers\v1\Question\QuestionViewController;
use App\Http\Controllers\v1\Question\QuestionVoteController;
use App\Http\Controllers\v1\QuestionController;
use Illuminate\Support\Facades\Route;

Route::controller(QuestionController::class)
    ->prefix('questions')
    ->name('questions.')
    ->group(function () {
        Route::get('/list', 'list')->name('list');
        Route::post('/propose', 'propose')->name('propose')->middleware('auth:sanctum');

        Route::post('/{question}/feedback', 'submitFeedback')
            ->name('feedback.submit')
            ->middleware('auth:sanctum');

        // comments
        Route::get('/{question}/comments', [CommentController::class, 'list'])
            ->name('comments.list');

        Route::post('/{question}/comments', [CommentController::class, 'store'])
            ->name('comments.store')
            ->middleware('auth:sanctum');

        // votes
        Route::put('/{question}/vote', [QuestionVoteController::class, 'update'])
            ->name('vote.update')
            ->middleware('auth:sanctum');

        // views
        Route::post('/{question}/view', [QuestionViewController::class, 'store'])
            ->middleware('throttle:question-view')
            ->name('view.store');
    });

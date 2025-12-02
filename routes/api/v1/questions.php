<?php

use App\Http\Controllers\v1\QuestionController;

Route::controller(QuestionController::class)
    ->prefix('questions')
    ->name('questions.')
    ->group(function () {
        Route::get('/list', 'list')->name('list');
        Route::post('/propose', 'propose')->name('propose')->middleware('auth:sanctum');

        Route::post('/{question}/feedback', 'submitFeedback')
            ->name('feedback.submit')
            ->middleware('auth:sanctum');
    });

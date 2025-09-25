<?php

use App\Http\Controllers\v1\GradeController;

Route::controller(GradeController::class)
    ->prefix('grades')
    ->name('grades.')
    ->group(function () {
        Route::get('/list', 'list')->name('list');
    });

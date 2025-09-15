<?php

use App\Http\Controllers\v1\PositionController;

Route::controller(PositionController::class)
    ->prefix('positions')
    ->name('positions.')
    ->group(function () {
        Route::get('/list', 'list')->name('list');
    });

<?php

use App\Http\Controllers\v1\TagController;

Route::controller(TagController::class)
    ->prefix('tags')
    ->name('tags.')
    ->group(function () {
        Route::get('/list', 'list')->name('list');
    });

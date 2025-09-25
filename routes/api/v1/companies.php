<?php

use App\Http\Controllers\v1\CompanyController;

Route::controller(CompanyController::class)
    ->prefix('companies')
    ->name('companies.')
    ->group(function () {
        Route::get('/list', 'list')->name('list');
    });

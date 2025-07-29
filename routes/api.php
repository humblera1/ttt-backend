<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
        Route::prefix('v1')->name('v1.')->group(function () {
            $path = __DIR__ . '/api/v1';

            require_once  "{$path}/auth.php";
        });
});

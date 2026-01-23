<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
        Route::prefix('v1')->name('v1.')->group(function () {
            $path = __DIR__ . '/api/v1';

            require_once "{$path}/auth.php";
            require_once "{$path}/positions.php";
            require_once "{$path}/companies.php";
            require_once "{$path}/tags.php";
            require_once "{$path}/grade.php";
            require_once "{$path}/questions.php";
            require_once "{$path}/notifications.php";
        });
});

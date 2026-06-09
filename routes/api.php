<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
        Route::prefix('v1')->name('v1.')->group(function () {
            $path = __DIR__ . '/api/v1';

            require "{$path}/auth.php";
            require "{$path}/positions.php";
            require "{$path}/companies.php";
            require "{$path}/tags.php";
            require "{$path}/grade.php";
            require "{$path}/questions.php";
            require "{$path}/notifications.php";
            require "{$path}/comments.php";
        });
});

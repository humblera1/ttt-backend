<?php

namespace App\Providers\v1\comments;

use App\Models\Comment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::bind('restorable_comment', function (string $value) {
            return Comment::withTrashed()->findOrFail($value);
        });
    }
}

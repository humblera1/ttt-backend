<?php

namespace App\Providers\v1;

use App\Services\api\v1\SettingsService;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('settings', function ($app) {
            return $app->make(SettingsService::class);
        });
    }
}

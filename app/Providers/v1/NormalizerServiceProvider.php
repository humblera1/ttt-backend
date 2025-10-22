<?php

namespace App\Providers\v1;

use App\Entities\Normalizer\Normalizer;
use App\Interfaces\v1\Normalization\NormalizerInterface;
use Illuminate\Support\ServiceProvider;

class NormalizerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(NormalizerInterface::class, Normalizer::class);
    }
}

<?php

namespace App\Providers\v1;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseCreatedServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('created', function () {
            return Response::noContent(HttpResponse::HTTP_CREATED);
        });
    }

}

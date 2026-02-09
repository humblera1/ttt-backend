<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\v1\comments\RouteServiceProvider::class,
    App\Providers\v1\NormalizerServiceProvider::class,
    App\Providers\v1\PasswordServiceProvider::class,
    App\Providers\v1\ResponseCreatedServiceProvider::class,
    App\Providers\v1\SettingsServiceProvider::class,
];

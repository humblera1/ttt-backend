<?php

use App\Providers\v1\{NormalizerServiceProvider,
    PasswordServiceProvider,
    ResponseCreatedServiceProvider,
    SettingsServiceProvider};

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    PasswordServiceProvider::class,
    SettingsServiceProvider::class,
    NormalizerServiceProvider::class,
    ResponseCreatedServiceProvider::class,
];

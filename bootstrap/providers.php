<?php

use App\Providers\v1\{NormalizerServiceProvider, PasswordServiceProvider, SettingsServiceProvider};

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    PasswordServiceProvider::class,
    SettingsServiceProvider::class,
    NormalizerServiceProvider::class,
];

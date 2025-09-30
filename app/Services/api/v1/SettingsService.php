<?php

namespace App\Services\api\v1;

use App\Models\Setting;
use Cache;

class SettingsService
{
    public function __construct
    (
        protected TypeService $service,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }

    public function all(): array
    {
        return Cache::rememberForever('global_settings', function () {
            return Setting::all()
                ->mapWithKeys(function (Setting $setting) {
                    return [
                        $setting->key => $this->service->convertStringToType($setting->value, $setting->type),
                    ];
                })
                ->toArray();
        });
    }

    public function clearCache(): void
    {
        Cache::forget('global_settings');
    }
}

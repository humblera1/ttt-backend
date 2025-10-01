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

    public function get(string $name, mixed $default = null): mixed
    {
        [$section, $key] = $this->getSectionKeyByName($name);

        $settings = $this->all();

        return $settings[$section][$key] ?? $default;
    }

    public function all(): array
    {
        return Cache::rememberForever('global_settings', function () {
            return Setting::all()
                ->groupBy('section')
                ->map(function ($settings) {
                    return $settings->mapWithKeys(fn (Setting $setting) => [
                        $setting->key => $this->service->convertStringToType($setting->value, $setting->type),
                    ]);
                })
                ->toArray();
        });
    }

    public function clearCache(): void
    {
        Cache::forget('global_settings');
    }

    protected function getSectionKeyByName(string $name): array
    {
        return explode('.', $name);
    }
}

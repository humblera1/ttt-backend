<?php

namespace App\Providers\v1;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class RedisMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // PhpRedisConnection lowercases dynamic method names; register the macro in lowercase.
        Redis::macro('setnxex', function (string $key, string $value, int $ttlSeconds): bool {
            return (bool) $this->set($key, $value, 'EX', $ttlSeconds, 'NX');
        });
    }
}

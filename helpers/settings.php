<?php

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

if (! function_exists('setting')) {
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app('settings')->get($key, $default);
    }
}

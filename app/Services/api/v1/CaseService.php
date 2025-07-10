<?php

namespace App\Services\api\v1;

class CaseService
{
    public function camelToKebabCase(string $string): string
    {
        $normalized = preg_replace('/(?<!^)[A-Z]/', '-$0', $string);

        return strtolower($normalized);
    }
}

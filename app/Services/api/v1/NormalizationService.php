<?php

namespace App\Services\api\v1;

class NormalizationService
{
    public function normalize(string $value): string
    {
        $v = trim($value);

        $v = preg_replace('/\s+/u', ' ', $v) ?? $v;

        $v = mb_strtolower($v);

        return $v;
    }
}

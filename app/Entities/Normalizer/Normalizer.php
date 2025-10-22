<?php

namespace App\Entities\Normalizer;

use App\Interfaces\v1\Normalization\NormalizerInterface;

class Normalizer implements NormalizerInterface
{
    public function normalize(string $value): string
    {
        $v = trim($value);

        $v = preg_replace('/\s+/u', ' ', $v) ?? $v;

        $v = mb_strtolower($v);

        return $v;
    }
}

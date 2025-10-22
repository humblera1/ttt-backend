<?php

namespace App\Interfaces\v1\Normalization;

interface NormalizerInterface
{
    public function normalize(string $value): string;
}

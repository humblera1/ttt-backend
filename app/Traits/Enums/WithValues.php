<?php

namespace App\Traits\Enums;

trait WithValues
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

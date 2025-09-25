<?php

namespace App\Enums;

enum Grade: string
{
    case Junior = 'Junior';
    case Middle = 'Middle';
    case Senior = 'Senior';
    case Lead = 'Lead';

    public static function values(): array
    {
        return array_map(fn (Grade $case) => $case->value, Grade::cases());
    }
}

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

    public static function colorByValue(string $value): string
    {
        return match ($value) {
            self::Junior->value => 'info',
            self::Middle->value => 'success',
            self::Senior->value => 'warning',
            self::Lead->value => 'danger',
            default => 'gray',
        };
    }
}

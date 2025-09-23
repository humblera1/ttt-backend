<?php

namespace App\Enums;

enum Status: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public static function values(): array
    {
        return array_map(fn (Status $case) => $case->value, Status::cases());
    }

    public static function options(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Approved->value => 'Approved',
            self::Rejected->value => 'Rejected',
        ];
    }

    public static function colorByValue(string $value): string
    {
        return match ($value) {
            self::Pending->value => 'warning',
            self::Approved->value => 'success',
            self::Rejected->value => 'danger',
            default => 'gray',
        };
    }
}


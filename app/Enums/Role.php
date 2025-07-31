<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Moderator = 'moderator';
    case PremiumUser = 'premium-user';
    case User = 'user';
    case Guest = 'guest';

    public static function colorByValue(string $value): string
    {
        return match ($value) {
            self::Admin->value => 'danger',
            self::Moderator->value => 'warning',
            self::PremiumUser->value => 'success',
            self::User->value => 'info',
            default => 'gray',
        };
    }
}

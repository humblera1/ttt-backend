<?php

namespace App\Enums\Notification;

enum RecipientMode: string
{
    case All = 'all';
    case ByUsername = 'by_username';

    public static function options(): array
    {
        return [
            self::All->value => 'All',
            self::ByUsername->value => 'By Username',
        ];
    }
}

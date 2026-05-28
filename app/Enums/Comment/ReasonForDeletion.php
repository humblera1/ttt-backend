<?php

namespace App\Enums\Comment;

use App\Traits\Enums\WithValues;

enum ReasonForDeletion: string
{
    use WithValues;

    case UserRemoved = 'user_removed';
    case Spam = 'spam';
    case Abuse = 'abuse';
    case Offtopic = 'offtopic';
    case Duplicate = 'duplicate';
    case Other = 'other';

    /**
     * Options for moderator deletion in Filament (excludes {@see UserRemoved}).
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Spam->value => 'Spam, advertising, violation of rules',
            self::Abuse->value => 'Abuse, harassment, inappropriate content',
            self::Offtopic->value => 'Off-topic, does not relate to the discussion',
            self::Duplicate->value => 'Duplicate of another comment',
            self::Other->value => 'Other...',
        ];
    }
}

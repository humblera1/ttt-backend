<?php

namespace App\Enums;

use App\Traits\Enums\WithValues;

enum QuestionRejectionReason: string
{
    use WithValues;

    case Duplicate= 'duplicate';
    case LowQuality = 'low_quality';
    case Offtopic = 'offtopic';
    case Incorrect = 'incorrect';
    case Spam = 'spam';
    case Incomplete = 'incomplete';
    case Other = 'other';

    public static function options(): array
    {
        return [
//            self::Duplicate->value => 'Duplicate of an existing question',
            self::LowQuality->value => 'Does not meet standards (low quality, response not disclosed)',
            self::Offtopic->value => 'Does not correspond to the platform\'s theme',
            self::Incorrect->value => 'Incorrect/erroneous question or answer',
            self::Spam->value => 'Spam, advertising, violation of rules',
            self::Incomplete->value => 'Insufficient information, registration not completed',
            self::Other->value => 'Other...',
        ];
    }
}

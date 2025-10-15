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
}

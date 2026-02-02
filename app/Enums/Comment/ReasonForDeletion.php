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
}

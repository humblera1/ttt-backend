<?php

namespace App\Enums\Vote;

enum UserVote: string
{
    case None = 'none';
    case Like = 'like';
    case Dislike = 'dislike';
}

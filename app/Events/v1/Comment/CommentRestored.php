<?php

namespace App\Events\v1\Comment;

use App\Models\Comment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentRestored
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Comment $comment,
    ) {}
}

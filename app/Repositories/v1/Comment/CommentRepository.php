<?php

namespace App\Repositories\v1\Comment;

use App\Models\Comment;
use App\Repositories\Repository;

class CommentRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Comment::class);
    }
}

<?php

namespace App\DTOs\v1\Comment;

use App\DTOs\BaseDTO;
use App\Models\User;

class CommentStoreDTO extends BaseDTO
{
    public function __construct(
        public readonly User $user,
        public readonly string $body,
        public readonly ?int $parentId = null,
    ) {}
}

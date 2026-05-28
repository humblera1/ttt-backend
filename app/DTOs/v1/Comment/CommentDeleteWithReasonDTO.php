<?php

namespace App\DTOs\v1\Comment;

use App\DTOs\BaseDTO;
use App\Enums\Comment\ReasonForDeletion;
use App\Models\Comment;
use App\Models\User;

class CommentDeleteWithReasonDTO extends BaseDTO
{
    public function __construct(
        public readonly Comment $comment,
        public readonly User $moderator,
        public readonly ReasonForDeletion $reason,
        public readonly ?string $reasonComment = null,
    ) {}
}

<?php

namespace App\DTOs\v1\Notification\Mappers\Contexts\Comments;

use App\DTOs\v1\Notification\Mappers\Contexts\PayloadContext;
use App\Models\Comment;
use App\Models\Question;
use App\Models\User;

final class CommentReplyNotificationPayloadContext extends PayloadContext
{
    public function __construct(
        public readonly Comment $reply,
        public readonly Question $question,
        public readonly User $respondent,
    ) {}
}

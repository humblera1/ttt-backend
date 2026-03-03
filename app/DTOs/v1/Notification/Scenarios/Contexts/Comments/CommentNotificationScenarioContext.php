<?php

namespace App\DTOs\v1\Notification\Scenarios\Contexts\Comments;

use App\DTOs\v1\Notification\Scenarios\Contexts\ScenarioContext;
use App\Models\Comment;

readonly class CommentNotificationScenarioContext extends ScenarioContext
{
    public function __construct(
        public Comment $comment,
    )
    {}
}

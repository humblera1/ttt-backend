<?php

namespace App\Mappers\Notification\Comments;

use App\DTOs\v1\Notification\Mappers\Contexts\Comments\CommentNotificationPayloadContext;
use App\DTOs\v1\Notification\Mappers\Contexts\PayloadContext;
use App\Mappers\Notification\NotificationPayloadMapper;
use Illuminate\Support\Str;

class NewCommentNotificationMapper extends NotificationPayloadMapper
{
    /**
     * @param CommentNotificationPayloadContext $context
     */
    public function map(PayloadContext $context): array
    {
        $reply = $context->reply;
        $question = $context->question;
        $respondent = $context->respondent;

        $questionTitle = Str::limit($question->title);
        $excerpt = Str::limit($reply->body, 120);

        return [
            'question_title' => $questionTitle,
            'comment_excerpt' => $excerpt,
            'comment_author' => $respondent->username,
        ];
    }
}

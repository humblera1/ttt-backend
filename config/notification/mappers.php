<?php

use App\Enums\Notification\NotificationCategory;
use App\Enums\Notification\NotificationType;
use App\Mappers\Notification\Comments\CommentReplyNotificationMapper;
use App\Mappers\Notification\Comments\NewCommentNotificationMapper;

return [
    NotificationCategory::ContentInteraction->value => [
        NotificationType::CommentReply->value => CommentReplyNotificationMapper::class,
        NotificationType::NewComment->value => NewCommentNotificationMapper::class,
    ],
];

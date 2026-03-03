<?php

use App\Enums\Notification\NotificationCategory;
use App\Enums\Notification\NotificationType;
use App\Mappers\Notification\Comments\CommentReplyNotificationMapper;

return [
    NotificationCategory::ContentInteraction->value => [
        NotificationType::CommentReply->value => CommentReplyNotificationMapper::class,
    ],
];

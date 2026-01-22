<?php

namespace App\DTOs\v1\Notification;

use App\DTOs\BaseDTO;
use App\Models\NotificationType;
use App\Models\User;

class FinalNotificationDTO extends BaseDTO
{
    public function __construct(
        public readonly NotificationType $type,
        public readonly string $title,
        public readonly string $body,
        public readonly array $data,
    )
    {}
}

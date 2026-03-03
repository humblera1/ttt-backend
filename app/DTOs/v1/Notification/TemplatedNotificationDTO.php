<?php

namespace App\DTOs\v1\Notification;

use App\DTOs\BaseDTO;

class TemplatedNotificationDTO extends BaseDTO
{
    public function __construct(
        public readonly string $categoryKey,
        public readonly string $typeKey,
        public readonly array $data,
    )
    {}
}

<?php

namespace App\DTOs\v1\Notification;

use App\DTOs\BaseDTO;
use App\Models\User;

class TemplatedNotificationDTO extends BaseDTO
{
    public function __construct(
        public readonly User $user,
        public readonly string $typeKey,
        public readonly array $data,
    )
    {}
}

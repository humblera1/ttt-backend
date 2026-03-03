<?php

namespace App\DTOs\v1\Notification\Scenarios\Instructions;

use App\DTOs\v1\Notification\TemplatedNotificationDTO;
use App\Models\User;

class InstructionToOne extends Instruction
{
    public function __construct(
        public readonly User $recipient,
        public readonly TemplatedNotificationDTO $notification,
    ) {}
}

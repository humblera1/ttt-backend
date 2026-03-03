<?php

namespace App\DTOs\v1\Notification\Scenarios\Instructions;

use App\DTOs\v1\Notification\TemplatedNotificationDTO;

class InstructionToMany extends Instruction
{
    public function __construct(
        /** @var int[] */
        public readonly array $recipientsIds,
        public readonly TemplatedNotificationDTO $notification,
    ) {}
}

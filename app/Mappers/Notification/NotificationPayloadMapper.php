<?php

namespace App\Mappers\Notification;

use App\DTOs\v1\Notification\Mappers\Contexts\PayloadContext;

abstract class NotificationPayloadMapper
{
    /**
     * @return array<string, mixed>
     */
    abstract public function map(PayloadContext $context): array;
}

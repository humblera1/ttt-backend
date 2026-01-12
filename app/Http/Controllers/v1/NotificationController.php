<?php

namespace App\Http\Controllers\v1;

use App\DTOs\v1\Notification\CustomNotificationDTO;
use App\DTOs\v1\Notification\TemplatedNotificationDTO;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\api\v1\Notification\NotificationService;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $service,
    )
    {}

    public function test(): void
    {
        $user = User::find(139);
        $typeKey = 'admin_message';
        $title = 'Тест';
        $body = 'Тестовое содержание';

        $dto = new CustomNotificationDTO(
            user: $user,
            typeKey: $typeKey,
            title: $title,
            body: $body,
        );

        $this->service->sendCustom($dto);
    }
}

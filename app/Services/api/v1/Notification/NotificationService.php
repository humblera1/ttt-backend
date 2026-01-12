<?php

namespace App\Services\api\v1\Notification;

use App\DTOs\v1\Notification\FinalNotificationDTO;
use App\DTOs\v1\Notification\TemplatedNotificationDTO;
use App\Entities\Notification\NotificationRenderer;
use App\Entities\Notification\NotificationSender;
use App\Exceptions\v1\BusinessLogicException;
use App\Repositories\v1\Notification\NotificationTypeRepository;
use Illuminate\Support\Facades\Log;

/**
 * Responsible for sending in-app notifications
 */
class NotificationService
{
    public function __construct(
        private readonly NotificationTypeRepository $repository,
        private readonly NotificationRenderer $renderer,
        private readonly NotificationSender $sender,
    )
    {}

    /**
     * Prepares and sends templated in-app notifications.
     */
    public function sendTemplated(TemplatedNotificationDTO $notification): void
    {
        try {
            $type = $this->repository->findByKeyOrFail($notification->typeKey);

            [$title, $body] = $this->renderer->render($type, $notification->data);

            $final = new FinalNotificationDTO(
                user: $notification->user,
                type: $type,
                title: $title,
                body: $body,
                data: $notification->data,
            );

            $this->sender->send($final);
        } catch (BusinessLogicException $e) {
            Log::error('Failed to send templated notification. Skipping.', ['exception' => $e]);
        }
    }
}

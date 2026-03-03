<?php

namespace App\Services\api\v1\Notification;

use App\DTOs\v1\Notification\CustomNotificationDTO;
use App\DTOs\v1\Notification\FinalNotificationDTO;
use App\DTOs\v1\Notification\TemplatedNotificationDTO;
use App\Entities\Notification\NotificationRenderer;
use App\Entities\Notification\NotificationSender;
use App\Exceptions\v1\BusinessLogicException;
use App\Models\User;
use App\Repositories\v1\Notification\NotificationTypeRepository;
use Illuminate\Support\Facades\Log;

/**
 * Responsible for sending in-app notifications.
 */
readonly class NotificationSendingService
{
    public function __construct(
        private NotificationTypeRepository $repository,
        private NotificationRenderer $renderer,
        private NotificationSender $sender,
    )
    {}

    /**
     * Prepares and sends templated in-app notifications.
     */
    public function sendTemplatedTo(User $user, TemplatedNotificationDTO $notification): void
    {
        try {
            $type = $this->repository->findInCategoryByKeysOrFail(
                $notification->categoryKey,
                $notification->typeKey,
            );

            [$title, $body] = $this->renderer->render($type, $notification->data);

            $final = new FinalNotificationDTO(
                type: $type,
                title: $title,
                body: $body,
                data: $notification->data,
            );

            $this->sender->sendTo($user, $final);
        } catch (BusinessLogicException $e) {
            Log::error('Failed to send templated notification. Skipping.', ['exception' => $e]);
        }
    }

    /**
     * Prepares and sends custom in-app notifications.
     */
    public function sendCustomTo(User $user, CustomNotificationDTO $notification): void
    {
        try {
            $type = $this->repository->findInCategoryByKeysOrFail(
                $notification->categoryKey,
                $notification->typeKey,
            );

            $final = new FinalNotificationDTO(
                type: $type,
                title: $notification->title,
                body: $notification->body,
                data: [],
            );

            $this->sender->sendTo($user, $final);
        } catch (BusinessLogicException $e) {
            Log::error('Failed to send custom notification. Skipping.', ['exception' => $e]);
        }
    }

    /**
     * Prepares and sends custom in-app notifications to provided users.
     */
    public function sendCustomToMany(array $usersIds, CustomNotificationDTO $notification): void
    {
        try {
            $type = $this->repository->findInCategoryByKeysOrFail(
                $notification->categoryKey,
                $notification->typeKey,
            );

            $final = new FinalNotificationDTO(
                type: $type,
                title: $notification->title,
                body: $notification->body,
                data: [],
            );

            $this->sender->sendToMany($usersIds, $final);
        } catch (BusinessLogicException $e) {
            Log::error('Failed to mass send custom notification. Skipping.', ['exception' => $e]);
        }
    }
}

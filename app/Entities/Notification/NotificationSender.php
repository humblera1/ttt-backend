<?php

namespace App\Entities\Notification;

use App\DTOs\v1\Notification\FinalNotificationDTO;
use App\Exceptions\v1\BusinessLogicException;
use App\Models\UserNotification;
use App\Repositories\v1\Notification\NotificationRepository;
use Exception;

readonly class NotificationSender
{
    public function __construct(
        private NotificationRepository $repository,
    )
    {}

    /**
     * Sends a ready-made in-app notification
     *
     * @throws BusinessLogicException
     */
    public function send(FinalNotificationDTO $notification): void
    {
        try {
            $model = new UserNotification();

            $model->user_id = $notification->user->id;
            $model->notification_type_id = $notification->type->id;
            $model->title = $notification->title;
            $model->body = $notification->body;
            $model->data = $notification->data;

            $this->repository->save($model);
        } catch (Exception $exception) {
            throw new BusinessLogicException($exception->getMessage());
        }
    }
}

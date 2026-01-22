<?php

namespace App\Entities\Notification;

use App\DTOs\v1\Notification\FinalNotificationDTO;
use App\Exceptions\v1\BusinessLogicException;
use App\Interfaces\v1\Notification\NotificationSenderInterface;
use App\Models\User;
use App\Models\UserNotification;
use App\Repositories\v1\Notification\NotificationRepository;
use Exception;

readonly class NotificationSender implements NotificationSenderInterface
{
    public function __construct(
        private NotificationRepository $repository,
    )
    {}

    /**
     * Sends a ready-made in-app notification to provided user.
     *
     * @throws BusinessLogicException
     */
    public function sendTo(User $user, FinalNotificationDTO $notification): void
    {
        try {
            $model = new UserNotification();

            $model->user_id = $user->id;
            $model->notification_type_id = $notification->type->id;
            $model->title = $notification->title;
            $model->body = $notification->body;
            $model->data = $notification->data;

            $this->repository->save($model);
        } catch (Exception $exception) {
            throw new BusinessLogicException($exception->getMessage());
        }
    }

    /**
     * Sends a ready-made in-app notification to provided bunch of users.
     *
     * @throws BusinessLogicException
     */
    public function sendToMany(iterable $users, FinalNotificationDTO $notification): void
    {
        $now = now();

        $rows = [];

        foreach ($users as $user) {
            $rows[] = [
                'user_id' => $user->id,
                'notification_type_id' => $notification->type->id,
                'title' => $notification->title,
                'body' => $notification->body,
                'data' => $notification->data,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($rows)) {
            return;
        }

        try {
            $this->repository->insert($rows);
        }catch (Exception $exception) {
            throw new BusinessLogicException($exception->getMessage());
        }
    }
}

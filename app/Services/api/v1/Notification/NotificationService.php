<?php

namespace App\Services\api\v1\Notification;

use App\DTOs\v1\Notification\CustomNotificationDTO;
use App\DTOs\v1\Notification\FinalNotificationDTO;
use App\DTOs\v1\Notification\TemplatedNotificationDTO;
use App\Entities\Notification\NotificationRenderer;
use App\Entities\Notification\NotificationSender;
use App\Exceptions\v1\BusinessLogicException;
use App\Exceptions\v1\RepositoryException;
use App\Models\User;
use App\Models\UserNotification;
use App\Repositories\v1\Notification\NotificationRepository;
use App\Repositories\v1\Notification\NotificationTypeRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/**
 * Responsible for working with in-app notifications.
 */
readonly class NotificationService
{
    public function __construct(
        private NotificationRepository $repository,
    )
    {}

    /**
     * Returns a list of notifications for the given user.
     */
    public function getNotificationsFor(User $user, ?int $perPage): LengthAwarePaginator
    {
        $perPage = $perPage ?? setting('notification.default_per_page', 10);

        $query = $user->notifications()->with('type', 'category');

        return $query->paginate($perPage);
    }

    /**
     * Marks the provided notification as read.
     *
     * @throws BusinessLogicException
     */
    public function markAsRead(UserNotification $notification): void
    {
        try {
            $notification->read_at = now();

            $this->repository->save($notification);
        } catch (RepositoryException $e) {
            Log::error('Failed to mark notification as read', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }
}

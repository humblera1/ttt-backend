<?php

namespace App\Repositories\v1\Notification;

use App\Exceptions\v1\RepositoryException;
use App\Models\User;
use App\Models\UserNotification;
use App\Repositories\Repository;
use Throwable;

class NotificationRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(UserNotification::class);
    }

    /**
     * @throws RepositoryException
     */
    public function markAllAsReadForUser(User $user): int
    {
        try {
            return $user->notifications()
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                ]);
        } catch (Throwable) {
            throw new RepositoryException('Failed to mark notifications as read');
        }
    }
}

<?php

namespace App\Repositories\v1\Notification;

use App\Models\UserNotification;
use App\Repositories\Repository;

class NotificationRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(UserNotification::class);
    }
}

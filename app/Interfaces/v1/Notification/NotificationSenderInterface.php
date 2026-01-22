<?php

namespace App\Interfaces\v1\Notification;

use App\DTOs\v1\Notification\FinalNotificationDTO;
use App\Models\User;

interface NotificationSenderInterface
{
    public function sendTo(User $user, FinalNotificationDTO $notification);

    public function sendToMany(iterable $users, FinalNotificationDTO $notification);
}

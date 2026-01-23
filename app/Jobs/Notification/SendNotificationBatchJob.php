<?php

namespace App\Jobs\Notification;

use App\DTOs\v1\Notification\CustomNotificationDTO;
use App\Enums\Queue\Queue;
use App\Services\api\v1\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationBatchJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly array $userIds,
        public readonly CustomNotificationDTO $notification,
    )
    {
        $this->onQueue(Queue::Notifications->value);
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $service): void
    {
        $service->sendCustomToMany($this->userIds, $this->notification);
    }
}

<?php

namespace App\Jobs;

use App\DTOs\v1\Notification\CustomNotificationDTO;
use App\Enums\Notification\RecipientMode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class DispatchMassAdminNotificationJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $queue = 'notifications';

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly CustomNotificationDTO $notification,
        public readonly RecipientMode $scope,
        public readonly array $userIds = [],
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}

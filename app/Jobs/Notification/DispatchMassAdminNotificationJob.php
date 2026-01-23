<?php

namespace App\Jobs\Notification;

use App\DTOs\v1\Notification\CustomNotificationDTO;
use App\Enums\Notification\RecipientMode;
use App\Enums\Queue\Queue;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class DispatchMassAdminNotificationJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly CustomNotificationDTO $notification,
        public readonly RecipientMode $mode,
        public readonly array $userIds = [],
    )
    {
        $this->onQueue(Queue::Notifications->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = $this->buildUserQuery();

        $query->chunkById(1000, function ($users) {
            SendNotificationBatchJob::dispatch(
                $users->pluck('id')->all(),
                $this->notification,
            );
        });
    }

    private function buildUserQuery(): Builder
    {
        $query = User::query();

        if ($this->mode === RecipientMode::ByUsername) {
            $query->whereIn('id', $this->userIds);
        }

        return $query;
    }
}

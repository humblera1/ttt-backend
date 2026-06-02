<?php

namespace App\Listeners\v1\Vote;

use App\Events\v1\Vote\VoteChanged;
use App\Services\api\v1\Vote\VotableLikesCountService;

readonly class UpdateVotableLikesCountSubscriber
{
    public function __construct(
        private VotableLikesCountService $service,
    ) {}

    public function handle(VoteChanged $event): void
    {
        $this->service->applyDelta($event->votable, $event->delta);
    }

    public function subscribe($events): void
    {
        $events->listen(
            VoteChanged::class,
            [self::class, 'handle'],
        );
    }
}

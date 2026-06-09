<?php

namespace App\Services\api\v1\Vote;

use App\Interfaces\v1\Vote\ModelVotesInterface;

class VotableLikesCountService
{
    /**
     * Applies a vote delta to the denormalized likes_count column (may go negative).
     */
    public function applyDelta(ModelVotesInterface $votable, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        $votable->newQuery()
            ->whereKey($votable->getKey())
            ->increment('likes_count', $delta);
    }
}

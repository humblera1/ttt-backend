<?php

namespace App\Services\api\v1\Vote;

use App\Events\v1\Vote\VoteChanged;
use App\Interfaces\v1\Vote\ModelVotesInterface;
use App\Models\User;
use App\Models\Vote;
use App\Repositories\v1\Vote\VoteRepository;
use Illuminate\Support\Facades\DB;

class VoteService
{
    public function __construct(
        protected VoteRepository $repository,
    ) {}

    /**
     * Sets the user's vote to like (1) or dislike (-1). Creates or switches value.
     * If the vote already has the requested value, returns 0 (no-op).
     *
     * @return int Delta applied to likes_count.
     */
    public function set(User $user, ModelVotesInterface $votable, int $value): int
    {
        $delta = DB::transaction(function () use ($user, $votable, $value) {
            $existing = $this->repository->findByUserAndVotable($user, $votable, lock: true);

            if ($existing === null) {
                return $this->createVote($user, $votable, $value);
            }

            if ($existing->value === $value) {
                return 0;
            }

            return $this->switchVote($existing, $value);
        });

        $this->dispatchVoteChanged($votable, $delta);

        return $delta;
    }

    /**
     * Removes the user's vote. Idempotent when no vote exists (delta 0).
     *
     * @return int Delta applied to likes_count.
     */
    public function clear(User $user, ModelVotesInterface $votable): int
    {
        $delta = DB::transaction(function () use ($user, $votable) {
            $existing = $this->repository->findByUserAndVotable($user, $votable, lock: true);

            if ($existing === null) {
                return 0;
            }

            return $this->removeVote($existing);
        });

        $this->dispatchVoteChanged($votable, $delta);

        return $delta;
    }

    private function createVote(User $user, ModelVotesInterface $votable, int $value): int
    {
        $this->repository->create($user, $votable, $value);

        return $value;
    }

    private function removeVote(Vote $vote): int
    {
        $delta = -$vote->value;

        $this->repository->delete($vote);

        return $delta;
    }

    private function switchVote(Vote $vote, int $value): int
    {
        $delta = $value - $vote->value;

        $this->repository->updateValue($vote, $value);

        return $delta;
    }

    private function dispatchVoteChanged(ModelVotesInterface $votable, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        event(new VoteChanged($votable, $delta));
    }
}

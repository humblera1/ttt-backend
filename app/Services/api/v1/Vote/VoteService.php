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
     * Toggles the user's vote on a votable entity (like / dislike / remove / switch).
     *
     * Dispatches {@see VoteChanged} after the DB transaction commits so listeners
     * only run on persisted vote rows.
     *
     * @return int Delta applied to likes_count (+1, -1, -2, etc.).
     */
    public function toggle(User $user, ModelVotesInterface $votable, int $value): int
    {
        $delta = DB::transaction(function () use ($user, $votable, $value) {
            $existing = $this->repository->findByUserAndVotable($user, $votable, lock: true);

            if ($existing === null) {
                return $this->createVote($user, $votable, $value);
            }

            if ($existing->value === $value) {
                return $this->removeVote($existing);
            }

            return $this->switchVote($existing, $value);
        });

        event(new VoteChanged($votable, $delta));

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
}

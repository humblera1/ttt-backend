<?php

namespace App\Repositories\v1\Vote;

use App\Exceptions\v1\RepositoryException;
use App\Interfaces\v1\Vote\ModelVotesInterface;
use App\Models\User;
use App\Models\Vote;
use App\Repositories\Repository;

class VoteRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Vote::class);
    }

    public function findByUserAndVotable(User $user, ModelVotesInterface $votable, bool $lock = false,): ?Vote
    {
        $query = Vote::query()
            ->where('user_id', $user->id)
            ->where('votable_type', $votable->getMorphClass())
            ->where('votable_id', $votable->getKey());

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * @throws RepositoryException
     */
    public function create(User $user, ModelVotesInterface $votable, int $value): Vote
    {
        $vote = new Vote([
            'user_id' => $user->id,
            'votable_id' => $votable->getKey(),
            'votable_type' => $votable->getMorphClass(),
            'value' => $value,
        ]);

        $this->save($vote);

        return $vote;
    }

    /**
     * @throws RepositoryException
     */
    public function updateValue(Vote $vote, int $value): Vote
    {
        $vote->value = $value;

        $this->save($vote);

        return $vote;
    }
}

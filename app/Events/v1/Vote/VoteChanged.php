<?php

namespace App\Events\v1\Vote;

use App\Interfaces\v1\Vote\ModelVotesInterface;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteChanged
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  ModelVotesInterface  $votable  Question or Comment instance.
     */
    public function __construct(
        public ModelVotesInterface $votable,
        public int $delta,
    ) {}
}

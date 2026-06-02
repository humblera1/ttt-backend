<?php

namespace App\Traits\Requests;

use App\Enums\Vote\UserVote;

trait WithUserVote
{
    public function userVote(): UserVote
    {
        return $this->enum('user_vote', UserVote::class);
    }
}

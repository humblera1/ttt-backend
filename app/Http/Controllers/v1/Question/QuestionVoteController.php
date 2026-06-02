<?php

namespace App\Http\Controllers\v1\Question;

use App\Enums\Vote\UserVote;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Vote\QuestionVoteUpdateRequest;
use App\Http\Resources\v1\Vote\VoteDeltaResource;
use App\Models\Question;
use App\Services\api\v1\Vote\VoteService;

class QuestionVoteController extends Controller
{
    public function __construct(
        private readonly VoteService $voteService,
    ) {}

    public function update(Question $question, QuestionVoteUpdateRequest $request): VoteDeltaResource
    {
        $user = $request->user();

        $delta = match ($request->userVote()) {
            UserVote::None => $this->voteService->clear($user, $question),
            UserVote::Like => $this->voteService->set($user, $question, 1),
            UserVote::Dislike => $this->voteService->set($user, $question, -1),
        };

        return new VoteDeltaResource($delta);
    }
}

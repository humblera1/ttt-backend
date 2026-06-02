<?php

namespace App\Http\Controllers\v1\Comment;

use App\Enums\Vote\UserVote;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Vote\CommentVoteUpdateRequest;
use App\Http\Resources\v1\Vote\VoteDeltaResource;
use App\Models\Comment;
use App\Services\api\v1\Vote\VoteService;

class CommentVoteController extends Controller
{
    public function __construct(
        private readonly VoteService $voteService,
    ) {}

    public function update(Comment $comment, CommentVoteUpdateRequest $request): VoteDeltaResource
    {
        $user = $request->user();

        $delta = match ($request->userVote()) {
            UserVote::None => $this->voteService->clear($user, $comment),
            UserVote::Like => $this->voteService->set($user, $comment, 1),
            UserVote::Dislike => $this->voteService->set($user, $comment, -1),
        };

        return new VoteDeltaResource($delta);
    }
}

<?php

namespace App\Http\Requests\v1\Vote;

use App\Enums\Vote\UserVote;
use App\Http\Requests\BaseFormRequest;
use App\Traits\Requests\WithUserVote;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Validation\Rule;

class CommentVoteUpdateRequest extends BaseFormRequest
{
    use WithUserVote;

    public function authorize(): bool
    {
        $user = $this->user();
        $comment = $this->route('comment');

        if (!$user instanceof User || !$comment instanceof Comment) {
            return false;
        }

        return $user->can('vote', $comment);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_vote' => ['required', Rule::enum(UserVote::class)],
        ];
    }
}

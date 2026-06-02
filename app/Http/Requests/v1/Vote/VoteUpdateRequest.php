<?php

namespace App\Http\Requests\v1\Vote;

use App\Enums\Vote\UserVote;
use App\Http\Requests\BaseFormRequest;
use App\Models\Question;
use App\Models\User;
use Illuminate\Validation\Rule;

class VoteUpdateRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $question = $this->route('question');

        if (!$user instanceof User || !$question instanceof Question) {
            return false;
        }

        return $user->can('vote', $question);
    }

    public function userVote(): UserVote
    {
        return $this->enum('user_vote', UserVote::class);
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

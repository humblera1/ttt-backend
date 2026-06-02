<?php

namespace App\Http\Requests\v1\Vote;

use App\Enums\Vote\UserVote;
use App\Http\Requests\BaseFormRequest;
use App\Traits\Requests\WithUserVote;
use App\Models\Question;
use App\Models\User;
use Illuminate\Validation\Rule;

class QuestionVoteUpdateRequest extends BaseFormRequest
{
    use WithUserVote;

    public function authorize(): bool
    {
        $user = $this->user();
        $question = $this->route('question');

        if (!$user instanceof User || !$question instanceof Question) {
            return false;
        }

        return $user->can('vote', $question);
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

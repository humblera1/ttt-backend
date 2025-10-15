<?php

namespace App\Services\api\v1;

use App\DTOs\v1\Question\QuestionsFilterDTO;
use App\Enums\QuestionRejectionReason;
use App\Enums\Status;
use App\Exceptions\v1\RepositoryException;
use App\Http\Filters\v1\Question\QuestionsListFilter;
use App\Models\Question;
use App\Repositories\v1\QuestionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class QuestionService
{
    public function __construct(
        protected QuestionRepository $repository,
    )
    {}

    public function getQuestionsList(QuestionsFilterDTO $dto): LengthAwarePaginator
    {
        return new QuestionsListFilter($dto)->apply()->paginate(setting('question.per_page', 15));
    }

    public function rejectQuestion(Question $question, ?string $reason, ?string $comment): bool
    {
        $question->rejection_reason = $reason;
        $question->rejection_comment = $comment;

        $question->status = Status::Rejected->value;

        try {
            $this->repository->save($question);
        } catch (RepositoryException $e) {
            Log::error('Failed to reject question', ['exception' => $e]);

            return false;
        }

        return true;
    }

    public function rejectQuestionAsDuplicate(Question $question, int $originalId, ?string $comment): bool
    {
        $original = Question::findOrFail($originalId);

        $question->rejection_reason = QuestionRejectionReason::Duplicate->value;
        $question->rejection_comment = $comment;

        $question->status = Status::Rejected->value;

        $question->duplicateOf()->associate($original);

        // todo: логика переноса статистических данных

        try {
            $this->repository->save($question);
        } catch (RepositoryException $e) {
            Log::error('Failed to reject question as duplicate', ['exception' => $e]);

            return false;
        }

        return true;
    }
}

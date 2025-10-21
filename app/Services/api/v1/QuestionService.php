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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    public function resetQuestion(Question $question): bool
    {
        $question->rejection_reason = null;
        $question->rejection_comment = null;
        $question->duplicate_of_id = null;

        $question->status = Status::Pending->value;

        try {
            $this->repository->save($question);
        } catch (RepositoryException $e) {
            Log::error('Failed to reset question', ['exception' => $e]);

            return false;
        }

        return true;
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
        DB::beginTransaction();

        try {
            $original = Question::findOrFail($originalId);

            $question->rejection_reason = QuestionRejectionReason::Duplicate->value;
            $question->rejection_comment = $comment;

            $question->status = Status::Rejected->value;

            $question->duplicateOf()->associate($original);

            $question->statistics()->update(['question_id' => $original->getKey()]);

            $this->repository->save($question);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Failed to reject question as duplicate', ['exception' => $e]);

            return false;
        }

        DB::commit();

        return true;
    }
}

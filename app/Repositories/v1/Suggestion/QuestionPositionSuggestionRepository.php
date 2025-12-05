<?php

namespace App\Repositories\v1\Suggestion;

use App\Enums\Suggestion\Status;
use App\Exceptions\v1\RepositoryException;
use App\Models\Question;
use App\Models\QuestionPositionSuggestion;
use App\Repositories\Repository;
use App\Traits\Repository\HasStatusWithReview;
use Throwable;

class QuestionPositionSuggestionRepository extends Repository
{
    use HasStatusWithReview;

    public function __construct()
    {
        parent::__construct(QuestionPositionSuggestion::class);
    }

    /**
     * @throws RepositoryException
     */
    public function incrementEvidence(int $questionId, int $positionId, int $threshold): void
    {
        $suggestion = $this->getOrCreateSuggestionForQuestionAndPosition($questionId, $positionId);

        $suggestion->evidence_count++;
        $suggestion->last_seen_at = now();

        if ($suggestion->evidence_count >= $threshold) {
            $suggestion->status = Status::ReadyForReview->value;
        }

        $this->save($suggestion);
    }

    public function attachPositionToQuestion(int $questionId, int $positionId): void
    {
        try {
            Question::findOrFail($questionId)
                ->positions()
                ->syncWithoutDetaching([$positionId]);
        } catch (Throwable) {
            throw new RepositoryException('Failed to attach position to question');
        }
    }

    protected function getOrCreateSuggestionForQuestionAndPosition(int $questionId, int $positionId): QuestionPositionSuggestion
    {
        // only in 'pending' status!
        return QuestionPositionSuggestion::firstOrCreate(
            [
                'question_id' => $questionId,
                'position_id' => $positionId,
                'status' => Status::Pending->value,
            ],
        );
    }
}

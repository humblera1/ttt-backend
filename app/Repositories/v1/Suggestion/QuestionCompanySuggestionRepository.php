<?php

namespace App\Repositories\v1\Suggestion;

use App\Enums\Suggestion\Status;
use App\Exceptions\v1\RepositoryException;
use App\Models\Question;
use App\Models\QuestionCompanySuggestion;
use App\Repositories\Repository;
use App\Traits\Repository\HasStatusWithReview;
use Throwable;

class QuestionCompanySuggestionRepository extends Repository
{
    use HasStatusWithReview;

    public function __construct()
    {
        parent::__construct(QuestionCompanySuggestion::class);
    }

    /**
     * @throws RepositoryException
     */
    public function incrementEvidence(int $questionId, int $companyId, int $threshold): void
    {
        $suggestion = $this->getOrCreateSuggestionForQuestionAndCompany($questionId, $companyId);

        $suggestion->evidence_count++;
        $suggestion->last_seen_at = now();

        if ($suggestion->evidence_count >= $threshold) {
            $suggestion->status = Status::ReadyForReview->value;
        }

        $this->save($suggestion);
    }

    public function attachCompanyToQuestion(int $questionId, int $companyId): void
    {
        try {
            Question::findOrFail($questionId)
                ->companies()
                ->syncWithoutDetaching([$companyId]);
        } catch (Throwable) {
            throw new RepositoryException('Failed to attach company to question');
        }
    }

    protected function getOrCreateSuggestionForQuestionAndCompany(int $questionId, int $companyId): QuestionCompanySuggestion
    {
        // only in 'pending' status!
        return QuestionCompanySuggestion::firstOrCreate(
            [
                'question_id' => $questionId,
                'company_id' => $companyId,
                'status' => Status::Pending->value,
            ],
        );
    }
}

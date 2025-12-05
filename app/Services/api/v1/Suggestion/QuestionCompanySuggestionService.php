<?php

namespace App\Services\api\v1\Suggestion;

use App\Exceptions\v1\RepositoryException;
use App\Models\QuestionCompanySuggestion;
use App\Models\Statistic;
use App\Repositories\v1\Company\CompanyRepository;
use App\Repositories\v1\Suggestion\QuestionCompanySuggestionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionCompanySuggestionService
{
    public function __construct(
        protected QuestionCompanySuggestionRepository $repository,
        protected CompanyRepository $companyRepository,
    )
    {}

    /**
     * Обрабатывает созданную статистику, в которой фигурирует компания.
     */
    public function handleStatistic(Statistic $statistic): void
    {
        $questionId = (int) $statistic->question_id;
        $companyId = (int) $statistic->company_id;

        $threshold = config('statistics.company_suggestion_evidence_threshold', 3);

        try {
            $this->repository->incrementEvidence($questionId, $companyId, $threshold);
        } catch (RepositoryException $e) {
            Log::error(
                'Failed to update question_company_suggestion while processing statistic',
                ['exception' => $e]
            );
        }
    }

    public function returnForReviewSuggestion(QuestionCompanySuggestion $suggestion): bool
    {
        try {
            $this->repository->returnForReviewById($suggestion->id);
        } catch (RepositoryException $e) {
            Log::error(
                'Failed to return question_company_suggestion for review',
                ['exception' => $e]
            );

            return false;
        }

        return true;
    }

    public function approveSuggestion(QuestionCompanySuggestion $suggestion): bool
    {
        DB::beginTransaction();

        try {
            $this->repository->approveById($suggestion->id);

            $this->repository->attachCompanyToQuestion(
                $suggestion->question_id,
                $suggestion->company_id
            );

            $this->companyRepository->approveById($suggestion->company_id);

            $this->repository->save($suggestion);
        } catch (RepositoryException $e) {
            Log::error(
                'Failed to approve question_company_suggestion',
                ['exception' => $e]
            );

            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
    }
}

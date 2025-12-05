<?php

namespace App\Services\api\v1\Suggestion;

use App\Exceptions\v1\RepositoryException;
use App\Models\QuestionCompanySuggestion;
use App\Models\QuestionPositionSuggestion;
use App\Models\Statistic;
use App\Repositories\v1\Position\PositionRepository;
use App\Repositories\v1\Suggestion\QuestionPositionSuggestionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionPositionSuggestionService
{
    public function __construct(
        protected QuestionPositionSuggestionRepository $repository,
        protected PositionRepository $positionRepository,
    )
    {}

    /**
     * Обрабатывает созданную статистику, в которой фигурирует компания.
     */
    public function handleStatistic(Statistic $statistic): void
    {
        $questionId = (int) $statistic->question_id;
        $positionId = (int) $statistic->position_id;

        $threshold = config('statistics.position_suggestion_evidence_threshold', 3);

        try {
            $this->repository->incrementEvidence($questionId, $positionId, $threshold);
        } catch (RepositoryException $e) {
            Log::error(
                'Failed to update question_position_suggestion while processing statistic',
                ['exception' => $e]
            );
        }
    }

    public function returnForReviewSuggestion(QuestionPositionSuggestion $suggestion): bool
    {
        try {
            $this->repository->returnForReviewById($suggestion->id);
        } catch (RepositoryException $e) {
            Log::error(
                'Failed to return question_position_suggestion for review',
                ['exception' => $e]
            );

            return false;
        }

        return true;
    }

    public function approveSuggestion(QuestionPositionSuggestion $suggestion): bool
    {
        DB::beginTransaction();

        try {
            $this->repository->approveById($suggestion->id);

            $this->repository->attachPositionToQuestion(
                $suggestion->question_id,
                $suggestion->position_id
            );

            $this->positionRepository->approveById($suggestion->position_id);

            $this->repository->save($suggestion);
        } catch (RepositoryException $e) {
            Log::error(
                'Failed to approve question_position_suggestion',
                ['exception' => $e]
            );

            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
    }
}

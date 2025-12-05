<?php

namespace App\Services\api\v1;

use App\DTOs\v1\Question\QuestionFeedbackDTO;
use App\Events\v1\Question\QuestionStatisticCreated;
use App\Exceptions\v1\RepositoryException;
use App\Interfaces\v1\Resolving\CompanyResolver;
use App\Interfaces\v1\Resolving\PositionResolver;
use App\Models\Question;
use App\Models\Statistic;
use App\Repositories\v1\StatisticsRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class QuestionFeedbackService
{
    public function __construct
    (
        protected StatisticsRepository $repository,
        protected CompanyResolver $companyResolver,
        protected PositionResolver $positionResolver,
    ) {}

    /**
     * Handle a completed user survey:
     *
     * - If previously the user answered that the question DID occur,
     *   and now `'met_in_real_interview' => false`, ignore;
     *
     * - If previously the user answered that the question did NOT occur,
     *   and now `'met_in_real_interview' => false`, ignore;
     *
     * - If previously the user answered that the question occurred
     *   for the same company and the same position, update the `when_asked` field;
     *
     * - If previously the user answered that the question occurred
     *   for a different company or a different position,
     *   and they have exceeded the allowed number of survey records for this question,
     *   do not create a new record;
     *
     * - If previously the user answered that the question occurred
     *   for a different company or a different position,
     *   and they have NOT exceeded the allowed number of survey records for this question,
     *   create a new record;
     *
     * - If the user has never filled out the survey for this question before,
     *   always create the first record.
     */
    public function saveFeedback(Question $question, QuestionFeedbackDTO $feedback): bool
    {
        DB::beginTransaction();

        try {
            $feedback->metInRealInterview
                ? $this->handleMet($question->id, $feedback)
                : $this->handleNotMet($question->id);
        } catch (Throwable $t) {
            Log::error('Failed to save question feedback', ['exception' => $t]);

            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
    }

    /**
     * Handles the case when a user says they have NOT encountered this question in an interview.
     * If there is any record (met or not_met), we assume that either the experience is already recorded,
     * or there is already a "not met" entry.
     * In both cases, a new "not met" answer does not add new information,
     * and we do not overwrite any existing met=true records.
     *
     * @throws RepositoryException
     */
    protected function handleNotMet(int $questionId): void
    {
        $userId = auth()->id();
        $statistic = $this->repository->findByUserAndQuestion($userId, $questionId);

        // Пользователь отвечает впервые
        if (!$statistic) {
            $this->repository->createNotMet($userId, $questionId);
        }
    }

    /**
     * Handles the case when a user says they HAVE encountered this question in an interview.
     *
     * @throws ContainerExceptionInterface
     * @throws RepositoryException
     * @throws NotFoundExceptionInterface
     */
    protected function handleMet(int $questionId, QuestionFeedbackDTO $feedback): void
    {
        $maxRecordsPerUserQuestion = setting('statistics.max_records_per_user_question', 3);

        $userId = auth()->id();

        // Удаляем ответ пользователя, в котором говорится, что вопрос не встречался
        $notMet = $this->repository->findNotMetByUserAndQuestion($userId, $questionId);

        if ($notMet) {
            $this->repository->delete($notMet);
        }

        $companyId = $this->resolveCompany($feedback->companyExisting, $feedback->companyNew);
        $positionId = $this->resolvePosition($feedback->positionExisting, $feedback->positionNew);

        $statistic = $this->repository->findByCombination($userId, $questionId, $companyId, $positionId);

        if ($statistic) {
            $statistic->when_asked = $feedback->whenAsked;

            $this->repository->save($statistic);

            return;
        }

        $recordsCount = $this->repository->countByUserQuestion($userId, $questionId);

        // пользователь превысил допустимое число опросов, тихо выходим, не засоряя статистику
        if ($recordsCount >= $maxRecordsPerUserQuestion) {
            return;
        }

        $statistic = new Statistic();

        $statistic->met_in_real_interview = true;
        $statistic->user_id = $userId;
        $statistic->question_id = $questionId;
        $statistic->company_id = $companyId;
        $statistic->position_id = $positionId;
        $statistic->when_asked = $feedback->whenAsked;

        $this->repository->save($statistic);

        QuestionStatisticCreated::dispatch($statistic);
    }

    protected function resolveCompany(?int $companyId, ?string $companyName): int
    {
        return $companyId ?? $this->companyResolver->resolveOne($companyName);
    }

    protected function resolvePosition(?int $positionId, ?string $positionName): int
    {
        return $positionId ?? $this->positionResolver->resolveOne($positionName);
    }
}

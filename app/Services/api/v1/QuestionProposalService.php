<?php

namespace App\Services\api\v1;

use App\DTOs\v1\Question\QuestionProposalDTO;
use App\Interfaces\v1\Resolving\CompanyResolver;
use App\Interfaces\v1\Resolving\PositionResolver;
use App\Interfaces\v1\Resolving\TagResolver;
use App\Models\Question;
use App\Models\Statistic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class QuestionProposalService
{
    public function __construct
    (
        protected TagResolver $tagResolver,
        protected CompanyResolver $companyResolver,
        protected PositionResolver $positionResolver,
    ) {}

    public function propose(QuestionProposalDTO $dto): bool
    {
        DB::beginTransaction();

        try {
            $question = new Question([
                'title' => $dto->title,
                'answer' => $dto->answer,
                'is_anonymous' => $dto->isAnonymous,
            ]);

            // todo: user_id

            $question->save();

            $this->bindTagsToQuestion($question, $dto->tagsExisting, $dto->tagsNew);
            $this->bindGradesToQuestion($question, $dto->grades);

            if (isset($dto->metInRealInterview)) {
                $statistic = new Statistic([
                    'met_in_real_interview' => $dto->metInRealInterview,
                ]);

                if ($dto->metInRealInterview === true) {
                    // todo: user_id, when_asked

                    $this->bindCompanyToStatistic($statistic, $dto->companyExisting, $dto->companyNew);
                    $this->bindPositionToStatistic($statistic, $dto->positionExisting, $dto->positionNew);
                }

                $question->statistics()->save($statistic);
            }
        } catch (Throwable $t) {
            Log::error('Failed to save question proposal', ['exception' => $t]);

            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
    }

    protected function bindTagsToQuestion(Question $question, array $tagIds, array $tagNames): void
    {
        $tagsToBind = array_unique(
            array_merge(
                $tagIds,
                $this->tagResolver->resolveMany($tagNames),
            )
        );

        if (!empty($tagsToBind)) {
            $question->tags()->sync($tagsToBind);
        }
    }

    protected function bindGradesToQuestion(Question $question, array $gradeIds): void
    {
        $gradesToBind = array_unique($gradeIds);

        if (!empty($gradesToBind)) {
            $question->grades()->sync($gradesToBind);
        }
    }

    protected function bindCompanyToStatistic(Statistic $statistic, ?int $companyId, ?string $companyName): void
    {
        if (!($companyId || $companyName)) {
            return;
        }

        if ($companyId) {
            $companyToBind = $companyId;
        } else {
            $companyToBind = $this->companyResolver->resolveOne($companyName);
        }

        if (empty($companyToBind)) {
            return;
        }

        $statistic->company()->associate($companyToBind);
    }

    public function bindPositionToStatistic(Statistic $statistic, ?int $positionId, ?string $positionName): void
    {
        if (!($positionId || $positionName)) {
            return;
        }

        if ($positionId) {
            $positionToBind = $positionId;
        } else {
            $positionToBind = $this->positionResolver->resolveOne($positionName);
        }

        if (empty($positionToBind)) {
            return;
        }

        $statistic->position()->associate($positionToBind);
    }
}

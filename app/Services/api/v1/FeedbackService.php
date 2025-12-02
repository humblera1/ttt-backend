<?php

namespace App\Services\api\v1;

use App\DTOs\v1\Question\QuestionFeedbackDTO;
use App\Interfaces\v1\Resolving\CompanyResolver;
use App\Interfaces\v1\Resolving\PositionResolver;
use App\Models\Question;
use App\Models\Statistic;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class FeedbackService
{
    public function __construct
    (
        protected CompanyResolver $companyResolver,
        protected PositionResolver $positionResolver,
    ) {}

    /**
     * Обработка пройденного пользователем опроса:
     *
     * - Если ранее пользователь отвечал, что вопрос встречался ему, а теперь `'met_in_real_interview' => false`,
     * обновляем существующую статистику;
     *
     * - Если ранее пользователь отвечал, что вопрос не встречался ему, и теперь `'met_in_real_interview' => false`,
     *  игнорируем данный опрос (дубликаты записей не несут смысловой нагрузки);
     *
     * - Если ранее пользователь отвечал, что вопрос встречался ему в ту же компанию на ту же должность, обновляем
     * поле `when_asked`;
     *
     * - Если ранее пользователь отвечал, что вопрос встречался ему на другую компанию или должность, и он превысил
     * допустимое число опросов на вопрос, не создаем новую запись;
     *
     *  - Если ранее пользователь отвечал, что вопрос встречался ему на другую компанию или должность, и он не превысил
     *  допустимое число опросов на вопрос, создаем новую запись;
     *
     * - Если пользователь ранее не проходил опрос для вопроса, создаем новую запись.
     *
     * todo: порефакторить метод
     */
    public function saveFeedback(Question $question, QuestionFeedbackDTO $feedback): bool
    {
        $user = auth()->user();

        $query = $question->statistics()
            ->where('user_id', $user->id);

        // Если не встречался на собеседовании
        if (!$feedback->metInRealInterview) {
            $statistic = (clone $query)->first();

            // пользователь впервые проходит опрос для данного вопроса
            if (!$statistic) {
                Statistic::create([
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                    'met_in_real_interview' => false,
                ]);

                return true;
            }

            // пользователь уже отвечал, что вопрос ему не встречался - выходим
            if (!$statistic->met_in_real_interview) {
                return true;
            }

            // пользователь отвечал, что вопрос встречался ему - обновляем статистику
            $statistic->met_in_real_interview = false;

            // todo: здесь нужно уменьшить счетчик у предложений связей вопрос-компания и вопрос-позиция
            // todo: здесь нужно обновить агрегат встреч у модели вопроса (уменьшить на 1)
            $statistic->company_id = null;
            $statistic->position_id = null;
            $statistic->when_asked = null;

            $statistic->save();

            return true;
        }

        DB::beginTransaction();

        // Если вопрос встречался
        try {
            $maxRecordsPerUserQuestion = setting('statistics.max_records_per_user_question');

            $companyId = $this->resolveCompany($feedback->companyExisting, $feedback->companyNew);
            $positionId = $this->resolvePosition($feedback->positionExisting, $feedback->positionNew);

            $statistic = (clone $query)
                ->where('company_id', $companyId)
                ->where('position_id', $positionId)
                ->first();

            // пользователь уже создавал данную комбинацию, но мог изменить when_asked
            if ($statistic) {
                $statistic->when_asked = $feedback->whenAsked;

                $statistic->save();

                DB::commit();

                return true;
            }

            // считаем все записи по user_id-question_id
            $recordsCount = (clone $query)->count();

            // пользователь превысил допустимое число опросов, тихо выходим, не засоряя статистику
            if ($recordsCount > $maxRecordsPerUserQuestion) {
                DB::commit();

                return true;
            }

            // наконец, создаем запись

            // todo: здесь нужно увеличить счетчик у предложений связей вопрос-компания и вопрос-позиция
            // todo: здесь нужно обновить агрегат встреч у модели вопроса (увеличить на 1)
            Statistic::create([
                'user_id' => $user->id,
                'question_id' => $question->id,
                'met_in_real_interview' => true,
                'company_id' => $companyId,
                'position_id' => $positionId,
                'when_asked' => $feedback->whenAsked,
            ]);
        } catch (Throwable $t) {
            Log::error('Failed to save question proposal', ['exception' => $t]);

            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
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

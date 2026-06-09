<?php

namespace App\Services\api\v1\Question;

use App\Models\Question;

/**
 * Maintains denormalized aggregate columns on {@see Question}.
 */
class QuestionAggregateService
{
    public function incrementCommentsCount(Question $question): void
    {
        $question->increment('comments_count');

        // todo: событие сигнализирует о необходимости пересчета рейтинга
        // event(new QuestionAggregatesUpdated($question));
    }

    public function decrementCommentsCount(Question $question): void
    {
        $question->decrement('comments_count');
    }

    /**
     * Decrement for met_in_real_interview_count is intentionally omitted: the domain does not delete
     *  or flip met=true statistics once counted (only not-met placeholders are removed before creating met).
     */
    public function incrementMetInRealInterviewCount(Question $question): void
    {
        $question->increment('met_in_real_interview_count');
    }
}

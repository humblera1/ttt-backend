<?php

namespace App\Services\api\v1\Question;

use App\Models\Question;

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
}

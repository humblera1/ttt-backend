<?php

namespace App\Traits\Question;

use App\Events\v1\Question\QuestionRatingNeedsRecalculation;

trait MarksQuestionForRatingRecalculation
{
    protected function markQuestionForRatingRecalculation(int $questionId): void
    {
        event(new QuestionRatingNeedsRecalculation($questionId));
    }
}

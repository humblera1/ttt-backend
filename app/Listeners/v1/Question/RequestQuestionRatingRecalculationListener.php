<?php

namespace App\Listeners\v1\Question;

use App\Events\v1\Question\QuestionRatingNeedsRecalculation;
use App\Services\api\v1\Question\QuestionRatingCoordinator;

readonly class RequestQuestionRatingRecalculationListener
{
    public function __construct(
        private QuestionRatingCoordinator $coordinator,
    ) {}

    public function handle(QuestionRatingNeedsRecalculation $event): void
    {
        $this->coordinator->requestRecalc($event->questionId);
    }
}

<?php

namespace App\Listeners\v1\Question;

use App\Events\v1\Question\QuestionStatisticCreated;
use App\Services\api\v1\Question\QuestionAggregateService;
use App\Services\api\v1\Question\QuestionRatingCoordinator;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateQuestionMetInRealInterviewCountListener implements ShouldQueue
{
    public function __construct(
        private readonly QuestionAggregateService $aggregateService,
        private readonly QuestionRatingCoordinator $coordinator,
    ) {}

    public function handle(QuestionStatisticCreated $event): void
    {
        if (!$event->statistic->met_in_real_interview) {
            return;
        }

        $question = $event->statistic->question;

        $this->aggregateService->incrementMetInRealInterviewCount($question);

        $this->coordinator->requestRecalc($question->id);
    }

    public function shouldQueue(QuestionStatisticCreated $event): bool
    {
        return $event->statistic->met_in_real_interview;
    }
}

<?php

namespace App\Listeners\v1\Question;

use App\Events\v1\Question\QuestionStatisticCreated;
use App\Services\api\v1\Question\QuestionAggregateService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateQuestionMetInRealInterviewCountListener implements ShouldQueue
{
    public function __construct(
        private readonly QuestionAggregateService $aggregateService,
    ) {}

    public function handle(QuestionStatisticCreated $event): void
    {
        $question = $event->statistic->question;

        $this->aggregateService->incrementMetInRealInterviewCount($question);
    }

    public function shouldQueue(QuestionStatisticCreated $event): bool
    {
        return $event->statistic->met_in_real_interview;
    }
}

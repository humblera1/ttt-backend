<?php

namespace App\Listeners\v1\Suggestion;

use App\Enums\Queue\Queue;
use App\Events\v1\Question\QuestionStatisticCreated;
use App\Services\api\v1\Suggestion\QuestionCompanySuggestionService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateQuestionCompanySuggestionListener implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = Queue::Suggestions->value;

    public function __construct(
        protected QuestionCompanySuggestionService $service,
    ) {}

    public function handle(QuestionStatisticCreated $event): void
    {
        $this->service->handleStatistic($event->statistic);
    }

    /**
     * Determine whether the listener should be queued.
     */
    public function shouldQueue(QuestionStatisticCreated $event): bool
    {
        return $event->statistic->met_in_real_interview
            && !is_null($event->statistic->company_id);
    }
}

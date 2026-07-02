<?php

namespace App\Listeners\v1\Question;

use App\Events\v1\Vote\VoteChanged;
use App\Models\Question;
use App\Services\api\v1\Question\QuestionRatingCoordinator;

readonly class RequestQuestionRatingRecalculationOnVoteListener
{
    public function __construct(
        private QuestionRatingCoordinator $coordinator,
    ) {}

    public function handle(VoteChanged $event): void
    {
        if ($event->delta === 0) {
            return;
        }

        if (!$event->votable instanceof Question) {
            return;
        }

        $this->coordinator->requestRecalc($event->votable->id);
    }
}

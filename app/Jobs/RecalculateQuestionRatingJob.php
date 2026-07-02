<?php

namespace App\Jobs;

use App\Services\api\v1\Question\QuestionRatingService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class RecalculateQuestionRatingJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $questionId,
    ) {}

    public function uniqueId(): string
    {
        return "question:{$this->questionId}";
    }

    public function uniqueFor(): int
    {
        return (int) setting('question.rate-limit-seconds', 60);
    }

    public function handle(QuestionRatingService $service): void
    {
        $lockKey = "rating:recalc:running:question:{$this->questionId}";

        $timeout = (int) setting('question.rate-limit-seconds', 60);

        Cache::lock($lockKey, $timeout)->block($timeout, function () use ($service): void {
            $service->recalculate($this->questionId);
        });
    }
}

<?php

namespace App\Services\api\v1\Question;

use App\Enums\Queue\Queue;
use App\Jobs\RecalculateQuestionRatingJob;
use App\Models\Question;
use Illuminate\Support\Facades\Cache;

/**
 * Debounces rating recalculation requests and dispatches a unique job per question.
 */
class QuestionRatingCoordinator
{
    public function requestRecalc(int $questionId): void
    {
        Question::query()->whereKey($questionId)->update(['rating_needs_recalculation' => true]);

        $lockKey = "rating:recalc:lock:question:{$questionId}";
        $ttl = (int) setting('question.rate-limit-seconds', 60);

        if (!Cache::add($lockKey, 1, $ttl)) {
            return;
        }

        RecalculateQuestionRatingJob::dispatch($questionId)->onQueue(Queue::Rating->value);
    }
}

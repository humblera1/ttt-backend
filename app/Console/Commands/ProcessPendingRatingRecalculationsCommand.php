<?php

namespace App\Console\Commands;

use App\Enums\Queue\Queue;
use App\Jobs\RecalculateQuestionRatingJob;
use App\Models\Question;
use Illuminate\Console\Command;

class ProcessPendingRatingRecalculationsCommand extends Command
{
    private const int CHUNK_SIZE = 200;

    protected $signature = 'rating:process-pending';

    protected $description = 'Dispatch rating recalculation jobs for questions flagged with rating_needs_recalculation';

    public function handle(): int
    {
        $dispatched = 0;

        Question::query()
            ->where('rating_needs_recalculation', true)
            ->orderBy('updated_at')
            ->limit(self::CHUNK_SIZE)
            ->pluck('id')
            ->each(function (int $questionId) use (&$dispatched): void {
                RecalculateQuestionRatingJob::dispatch($questionId)->onQueue(Queue::Rating->value);
                $dispatched++;
            });

        $this->info("Dispatched {$dispatched} rating recalculation job(s).");

        return self::SUCCESS;
    }
}

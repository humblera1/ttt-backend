<?php

namespace Feature\v1\Question;

use App\Jobs\RecalculateQuestionRatingJob;
use App\Models\Question;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ProcessPendingRatingRecalculationsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_process_pending_command_dispatches_jobs_for_flagged_questions(): void
    {
        Bus::fake();

        $first = Question::factory()->common()->create(['rating_needs_recalculation' => true]);
        $second = Question::factory()->common()->create(['rating_needs_recalculation' => true]);

        Artisan::call('rating:process-pending');

        Bus::assertDispatched(RecalculateQuestionRatingJob::class, function (RecalculateQuestionRatingJob $job) use ($first) {
            return $job->questionId === $first->id;
        });

        Bus::assertDispatched(RecalculateQuestionRatingJob::class, function (RecalculateQuestionRatingJob $job) use ($second) {
            return $job->questionId === $second->id;
        });
    }

    public function test_process_pending_skips_questions_without_flag(): void
    {
        Bus::fake();

        $question = Question::factory()->common()->create(['rating_needs_recalculation' => false]);

        Artisan::call('rating:process-pending');

        Bus::assertNotDispatched(RecalculateQuestionRatingJob::class, function (RecalculateQuestionRatingJob $job) use ($question) {
            return $job->questionId === $question->id;
        });
    }
}

<?php

namespace Feature\v1\Question;

use App\Jobs\RecalculateQuestionRatingJob;
use App\Listeners\v1\Question\RequestQuestionRatingRecalculationListener;
use App\Models\Question;
use App\Services\api\v1\Question\QuestionRatingCoordinator;
use App\Traits\Tests\ClearsTestTables;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class QuestionRatingCoordinatorTest extends TestCase
{
    use ClearsTestTables, DatabaseTransactions;

    private QuestionRatingCoordinator $coordinator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearQuestionsAndDependencies();

        Cache::flush();

        $this->coordinator = app(QuestionRatingCoordinator::class);
    }

    public function test_request_recalc_sets_rating_needs_recalculation(): void
    {
        $question = Question::factory()->common()->create([
            'rating_needs_recalculation' => false,
        ]);

        $this->coordinator->requestRecalc($question->id);

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'rating_needs_recalculation' => 1,
        ]);
    }

    public function test_request_recalc_dispatches_unique_job_on_first_call(): void
    {
        Bus::fake();

        $question = Question::factory()->common()->create();

        $this->coordinator->requestRecalc($question->id);

        Bus::assertDispatched(RecalculateQuestionRatingJob::class, function (RecalculateQuestionRatingJob $job) use ($question) {
            return $job->questionId === $question->id;
        });
    }

    public function test_request_recalc_skips_second_dispatch_within_debounce_window(): void
    {
        Bus::fake();

        $question = Question::factory()->common()->create([
            'rating_needs_recalculation' => false,
        ]);

        $this->coordinator->requestRecalc($question->id);
        $this->coordinator->requestRecalc($question->id);

        Bus::assertDispatchedTimes(RecalculateQuestionRatingJob::class, 1);

        $this->assertTrue($question->fresh()->rating_needs_recalculation);
    }

    public function test_listener_is_synchronous(): void
    {
        $this->assertFalse(
            is_subclass_of(RequestQuestionRatingRecalculationListener::class, ShouldQueue::class),
        );
    }
}

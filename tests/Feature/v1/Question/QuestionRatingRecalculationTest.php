<?php

namespace Feature\v1\Question;

use App\Enums\Period;
use App\Events\v1\Question\QuestionRatingNeedsRecalculation;
use App\Events\v1\Question\QuestionStatisticCreated;
use App\Jobs\RecalculateQuestionRatingJob;
use App\Listeners\v1\Question\UpdateQuestionMetInRealInterviewCountListener;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Question;
use App\Models\Statistic;
use App\Models\User;
use App\Services\api\v1\Question\QuestionViewService;
use App\Traits\Tests\WithUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class QuestionRatingRecalculationTest extends TestCase
{
    use DatabaseTransactions, WithUser;

    protected string $permission = 'propose-question';

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('statistics')->delete();
        DB::table('question_company_suggestions')->delete();
        DB::table('question_position_suggestions')->delete();
        DB::table('votes')->delete();
        DB::table('comments')->delete();
        DB::table('questions')->delete();
        DB::table('companies')->delete();
    }

    public function test_vote_on_question_triggers_rating_recalc_pipeline(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $user->givePermissionTo(['vote-question', 'view-any-question']);
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'likes_count' => 0,
            'rating_needs_recalculation' => false,
        ]);

        $response = $this->putJson(route('api.v1.questions.vote.update', $question), [
            'user_vote' => 'like',
        ]);

        $response->assertOk();

        $this->assertTrue($question->fresh()->rating_needs_recalculation);
        Bus::assertDispatched(RecalculateQuestionRatingJob::class);
    }

    public function test_vote_on_comment_does_not_flag_parent_question_rating_recalculation(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $user->givePermissionTo(['vote-comment', 'view-any-comment']);
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'rating_needs_recalculation' => false,
        ]);

        $author = User::factory()->create();
        $comment = new Comment([
            'body' => 'Vote target',
            'likes_count' => 0,
        ]);
        $comment->user()->associate($author);
        $comment->question()->associate($question);
        $comment->save();

        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), [
            'user_vote' => 'like',
        ]);

        $response->assertOk();

        $this->assertFalse($question->fresh()->rating_needs_recalculation);
        Bus::assertNotDispatched(RecalculateQuestionRatingJob::class);
    }

    public function test_statistic_met_true_triggers_rating_recalc_pipeline(): void
    {
        Bus::fake();

        $user = $this->getUser();
        $this->actingAs($user);

        $company = Company::query()->create(['name' => 'Rating Acme']);

        $response = $this->postJson(route('api.v1.questions.propose'), [
            'title' => 'Rating met statistic test',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::LastMonth->value,
                'companyExisting' => $company->id,
            ],
        ]);

        $response->assertCreated();

        $question = Question::query()->where('title', 'Rating met statistic test')->firstOrFail();

        $this->dispatchMetStatisticCreatedEvent($question);

        $this->assertTrue($question->fresh()->rating_needs_recalculation);
        Bus::assertDispatched(RecalculateQuestionRatingJob::class);
    }

    public function test_views_flush_does_not_dispatch_rating_event_per_id(): void
    {
        if (!$this->redisIsAvailable()) {
            $this->markTestSkipped('Redis is not available.');
        }

        Event::fake([QuestionRatingNeedsRecalculation::class]);

        $question = Question::factory()->common()->create([
            'views_count' => 0,
            'rating_needs_recalculation' => false,
        ]);

        $viewService = app(QuestionViewService::class);
        Redis::hIncrBy($viewService->bufferKey(), (string) $question->id, 2);

        Artisan::call('views:flush');

        Event::assertNotDispatched(QuestionRatingNeedsRecalculation::class);
        $this->assertTrue($question->fresh()->rating_needs_recalculation);
    }

    public function test_recalculate_job_clears_flag_after_success(): void
    {
        $question = Question::factory()->common()->create([
            'rating' => 42,
            'rating_needs_recalculation' => true,
        ]);

        $job = new RecalculateQuestionRatingJob($question->id);
        $job->handle(app(\App\Services\api\v1\Question\QuestionRatingService::class));

        $fresh = $question->fresh();

        $this->assertFalse($fresh->rating_needs_recalculation);
        $this->assertSame(42, $fresh->rating);
    }

    private function dispatchMetStatisticCreatedEvent(Question $question): void
    {
        $statistic = Statistic::query()
            ->where('question_id', $question->id)
            ->where('met_in_real_interview', true)
            ->latest('id')
            ->firstOrFail();

        app(UpdateQuestionMetInRealInterviewCountListener::class)
            ->handle(new QuestionStatisticCreated($statistic));
    }

    private function redisIsAvailable(): bool
    {
        try {
            Redis::ping();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}

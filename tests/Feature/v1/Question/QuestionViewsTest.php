<?php

namespace Feature\v1\Question;

use App\Enums\Status;
use App\Models\Question;
use App\Models\User;
use App\Services\api\v1\Question\QuestionViewService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class QuestionViewsTest extends TestCase
{
    use DatabaseTransactions;

    private QuestionViewService $viewService;

    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->redisIsAvailable()) {
            $this->markTestSkipped('Redis is not available.');
        }

        $this->viewService = app(QuestionViewService::class);

        DB::table('statistics')->delete();
        DB::table('question_company_suggestions')->delete();
        DB::table('question_position_suggestions')->delete();
        DB::table('questions')->delete();

        $this->clearRedisViewKeys();
        RateLimiter::clear('question-view');
    }

    protected function tearDown(): void
    {
        $this->clearRedisViewKeys();
        RateLimiter::clear('question-view');

        parent::tearDown();
    }

    public function test_guest_can_record_view_for_approved_question(): void
    {
        $question = $this->approvedQuestion();

        $response = $this->postJson(route('api.v1.questions.view.store', $question));

        $response->assertNoContent();
    }

    public function test_authenticated_user_can_record_view(): void
    {
        $question = $this->approvedQuestion();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('api.v1.questions.view.store', $question));

        $response->assertNoContent();
    }

    public function test_pending_question_returns_not_found(): void
    {
        $question = Question::factory()->common()->create([
            'status' => Status::Pending->value,
        ]);

        $response = $this->postJson(route('api.v1.questions.view.store', $question));

        $response->assertNotFound();
    }

    public function test_rejected_question_returns_not_found(): void
    {
        $question = Question::factory()->common()->create([
            'status' => Status::Rejected->value,
        ]);

        $response = $this->postJson(route('api.v1.questions.view.store', $question));

        $response->assertNotFound();
    }

    public function test_nonexistent_question_returns_not_found(): void
    {
        $response = $this->postJson(route('api.v1.questions.view.store', ['question' => 999999]));

        $response->assertNotFound();
    }

    public function test_rate_limiter_returns_429_when_exceeded(): void
    {
        RateLimiter::for('question-view', function (Request $request) {
            return Limit::perMinute(2)->by($request->ip());
        });

        $question = $this->approvedQuestion();

        $this->postJson(route('api.v1.questions.view.store', $question))->assertNoContent();
        $this->postJson(route('api.v1.questions.view.store', $question))->assertNoContent();

        $response = $this->postJson(route('api.v1.questions.view.store', $question));

        $response->assertStatus(429);
    }

    public function test_dedup_within_ttl_does_not_double_buffer(): void
    {
        $question = $this->approvedQuestion();

        $this->postJson(route('api.v1.questions.view.store', $question))->assertNoContent();
        $this->postJson(route('api.v1.questions.view.store', $question))->assertNoContent();

        $bufferValue = Redis::hGet($this->viewService->bufferKey(), (string) $question->id);

        $this->assertSame('1', $bufferValue);
    }

    private function approvedQuestion(): Question
    {
        return Question::factory()->common()->create([
            'status' => Status::Approved->value,
            'views_count' => 0,
            'rating_needs_recalculation' => false,
        ]);
    }

    private function clearRedisViewKeys(): void
    {
        $viewService = app(QuestionViewService::class);

        Redis::del($viewService->bufferKey());
        Redis::del($viewService->bufferTmpKey());

        $prefix = (string) config('question_views.redis.dedup_key_prefix');
        $dedupKeys = Redis::keys($prefix.'*');

        if ($dedupKeys !== false && $dedupKeys !== []) {
            Redis::del(...$dedupKeys);
        }
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

<?php

namespace Feature\v1\Question;

use App\Enums\Status;
use App\Models\Question;
use App\Services\api\v1\Question\QuestionViewService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class FlushQuestionViewsTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        $this->clearRedisViewKeys();

        parent::tearDown();
    }

    public function test_flush_increments_views_count_in_database(): void
    {
        $question = Question::factory()->common()->create([
            'status' => Status::Approved->value,
            'views_count' => 5,
            'rating_needs_recalculation' => false,
        ]);

        Redis::hIncrBy($this->viewService->bufferKey(), (string) $question->id, 3);

        Artisan::call('views:flush');

        $this->assertSame(8, $question->fresh()->views_count);
    }

    public function test_flush_sets_rating_needs_recalculation_for_touched_questions(): void
    {
        $question = Question::factory()->common()->create([
            'status' => Status::Approved->value,
            'views_count' => 0,
            'rating_needs_recalculation' => false,
        ]);

        Redis::hIncrBy($this->viewService->bufferKey(), (string) $question->id, 1);

        Artisan::call('views:flush');

        $this->assertTrue($question->fresh()->rating_needs_recalculation);
    }

    public function test_flush_with_empty_buffer_is_no_op(): void
    {
        $question = Question::factory()->common()->create([
            'status' => Status::Approved->value,
            'views_count' => 10,
            'rating_needs_recalculation' => false,
        ]);

        Artisan::call('views:flush');

        $fresh = $question->fresh();

        $this->assertSame(10, $fresh->views_count);
        $this->assertFalse($fresh->rating_needs_recalculation);
    }

    public function test_record_view_skips_nonexistent_question_in_service(): void
    {
        $question = Question::factory()->common()->create([
            'status' => Status::Approved->value,
        ]);

        $questionId = $question->id;
        $question->delete();

        $deleted = Question::withTrashed()->find($questionId);
        $this->assertNotNull($deleted);

        app(QuestionViewService::class)->recordView($deleted, '127.0.0.1');

        $bufferValue = Redis::hGet($this->viewService->bufferKey(), (string) $questionId);

        $this->assertFalse($bufferValue);
    }

    private function clearRedisViewKeys(): void
    {
        Redis::del($this->viewService->bufferKey());
        Redis::del($this->viewService->bufferTmpKey());

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

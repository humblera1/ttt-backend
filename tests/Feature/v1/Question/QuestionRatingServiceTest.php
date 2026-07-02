<?php

namespace Feature\v1\Question;

use App\Jobs\RecalculateQuestionRatingJob;
use App\Models\Question;
use App\Models\User;
use App\Services\api\v1\Question\QuestionRatingCalculator;
use App\Services\api\v1\Question\QuestionRatingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuestionRatingServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('votes')->delete();
    }

    public function test_recalculate_persists_rating_and_clears_flag(): void
    {
        $question = Question::factory()->common()->create([
            'likes_count' => 5,
            'views_count' => 9,
            'comments_count' => 4,
            'met_in_real_interview_count' => 2,
            'rating' => 0,
            'rating_needs_recalculation' => true,
        ]);

        $expectedRating = app(QuestionRatingCalculator::class)->calculate($question->fresh());

        app(QuestionRatingService::class)->recalculate($question->id);

        $fresh = $question->fresh();

        $this->assertSame($expectedRating, $fresh->rating);
        $this->assertFalse($fresh->rating_needs_recalculation);
    }

    public function test_job_and_process_pending_use_same_calculator(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['vote-question', 'view-any-question']);
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'likes_count' => 0,
            'views_count' => 0,
            'comments_count' => 0,
            'met_in_real_interview_count' => 0,
            'rating' => 0,
            'rating_needs_recalculation' => false,
        ]);

        $this->putJson(route('api.v1.questions.vote.update', $question), [
            'user_vote' => 'like',
        ])->assertOk();

        $question->refresh();

        $job = new RecalculateQuestionRatingJob($question->id);
        $job->handle(app(QuestionRatingService::class));

        $expectedRating = app(QuestionRatingCalculator::class)->calculate($question->fresh());

        $this->assertSame($expectedRating, $question->fresh()->rating);
        $this->assertSame(1, $question->fresh()->likes_count);
    }
}

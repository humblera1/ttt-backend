<?php

namespace Feature\v1\Comment;

use App\DTOs\v1\Comment\CommentStoreDTO;
use App\Events\v1\Comment\CommentCreated;
use App\Events\v1\Comment\CommentDeleted;
use App\Events\v1\Comment\CommentRestored;
use App\Jobs\RecalculateQuestionRatingJob;
use App\Listeners\v1\Question\RequestQuestionRatingRecalculationOnCommentListener;
use App\Listeners\v1\Question\UpdateQuestionCommentsCountListener;
use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use App\Services\api\v1\Comment\CommentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class QuestionCommentsCountAndRatingTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_update_question_comments_count_listener_is_not_queued(): void
    {
        $this->assertFalse(
            is_subclass_of(UpdateQuestionCommentsCountListener::class, ShouldQueue::class),
        );
    }

    public function test_request_question_rating_recalculation_on_comment_listener_is_not_queued(): void
    {
        $this->assertFalse(
            is_subclass_of(RequestQuestionRatingRecalculationOnCommentListener::class, ShouldQueue::class),
        );
    }

    public function test_create_comment_increments_comments_count_synchronously(): void
    {
        $user = $this->userWhoCanManageOwnComments();
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'comments_count' => 0,
        ]);

        $response = $this->postJson(route('api.v1.questions.comments.store', $question), [
            'body' => 'Sync aggregate test',
        ]);

        $response->assertCreated();

        $this->assertSame(1, $question->fresh()->comments_count);
    }

    public function test_delete_comment_decrements_comments_count_synchronously(): void
    {
        $user = $this->userWhoCanManageOwnComments();
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'comments_count' => 0,
        ]);

        $comment = $this->createCommentForQuestion($question, $user);

        $response = $this->deleteJson(route('api.v1.comments.comments.delete', $comment));

        $response->assertNoContent();

        $this->assertSame(0, $question->fresh()->comments_count);
    }

    public function test_restore_comment_increments_comments_count_synchronously(): void
    {
        $user = $this->userWhoCanManageOwnComments();
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'comments_count' => 0,
        ]);

        $comment = $this->createCommentForQuestion($question, $user);

        $this->deleteJson(route('api.v1.comments.comments.delete', $comment))->assertNoContent();
        $this->assertSame(0, $question->fresh()->comments_count);

        $response = $this->postJson(route('api.v1.comments.comments.restore', $comment));

        $response->assertOk();

        $this->assertSame(1, $question->fresh()->comments_count);
    }

    public function test_comment_created_dispatches_comment_created_event(): void
    {
        Event::fake([CommentCreated::class]);

        $user = User::factory()->create();
        $question = Question::factory()->common()->create();

        app(CommentService::class)->createForQuestion($question, new CommentStoreDTO(
            user: $user,
            body: 'Event dispatch test',
            parentId: null,
        ));

        Event::assertDispatched(CommentCreated::class);
    }

    public function test_comment_deleted_dispatches_comment_deleted_event(): void
    {
        Event::fake([CommentDeleted::class]);

        $user = User::factory()->create();
        $question = Question::factory()->common()->create();
        $comment = $this->createCommentForQuestion($question, $user);

        app(CommentService::class)->deleteByUser($comment, $user);

        Event::assertDispatched(CommentDeleted::class);
    }

    public function test_comment_created_triggers_rating_recalc_pipeline(): void
    {
        Bus::fake();

        $user = $this->userWhoCanManageOwnComments();
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'comments_count' => 0,
            'rating_needs_recalculation' => false,
        ]);

        $this->postJson(route('api.v1.questions.comments.store', $question), [
            'body' => 'Rating pipeline test',
        ])->assertCreated();

        $this->assertTrue($question->fresh()->rating_needs_recalculation);
        Bus::assertDispatched(RecalculateQuestionRatingJob::class);
    }

    public function test_comment_deleted_triggers_rating_recalc_pipeline(): void
    {
        Bus::fake();

        $user = $this->userWhoCanManageOwnComments();
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'comments_count' => 0,
            'rating_needs_recalculation' => false,
        ]);

        $this->postJson(route('api.v1.questions.comments.store', $question), [
            'body' => 'Comment to delete',
        ])->assertCreated();

        $comment = Comment::query()->where('question_id', $question->id)->firstOrFail();

        $this->deleteJson(route('api.v1.comments.comments.delete', $comment))->assertNoContent();

        $this->assertTrue($question->fresh()->rating_needs_recalculation);
        Bus::assertDispatched(RecalculateQuestionRatingJob::class);
    }

    public function test_comment_restored_triggers_rating_recalc_pipeline(): void
    {
        Bus::fake();

        $user = $this->userWhoCanManageOwnComments();
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'comments_count' => 0,
            'rating_needs_recalculation' => false,
        ]);

        $this->postJson(route('api.v1.questions.comments.store', $question), [
            'body' => 'Comment to restore',
        ])->assertCreated();

        $comment = Comment::query()->where('question_id', $question->id)->firstOrFail();

        Cache::flush();

        $this->deleteJson(route('api.v1.comments.comments.delete', $comment))->assertNoContent();

        Cache::flush();

        $this->postJson(route('api.v1.comments.comments.restore', $comment))->assertOk();

        $this->assertTrue($question->fresh()->rating_needs_recalculation);
        Bus::assertDispatched(RecalculateQuestionRatingJob::class);
    }

    public function test_multiple_comments_debounced_to_single_job_within_rate_limit(): void
    {
        Bus::fake();

        $user = $this->userWhoCanManageOwnComments();
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'comments_count' => 0,
        ]);

        foreach (range(1, 5) as $index) {
            $this->postJson(route('api.v1.questions.comments.store', $question), [
                'body' => "Comment {$index}",
            ])->assertCreated();
        }

        Bus::assertDispatchedTimes(RecalculateQuestionRatingJob::class, 1);
        $this->assertTrue($question->fresh()->rating_needs_recalculation);
    }

    private function userWhoCanManageOwnComments(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo([
            'create-comment',
            'delete-own-comment',
            'restore-own-comment',
        ]);

        return $user;
    }

    private function createCommentForQuestion(Question $question, User $user): Comment
    {
        return app(CommentService::class)->createForQuestion($question, new CommentStoreDTO(
            user: $user,
            body: 'Test comment',
            parentId: null,
        ));
    }
}

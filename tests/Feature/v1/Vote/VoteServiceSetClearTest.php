<?php

namespace Feature\v1\Vote;

use App\Events\v1\Vote\VoteChanged;
use App\Listeners\v1\Vote\UpdateVotableLikesCountSubscriber;
use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use App\Services\api\v1\Vote\VoteService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VoteServiceSetClearTest extends TestCase
{
    use DatabaseTransactions;

    private VoteService $service;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('votes')->delete();

        $this->service = app(VoteService::class);
    }

    public function test_set_creates_vote_and_increments_likes_count(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['likes_count' => 0]);

        $delta = $this->service->set($user, $question, 1);

        $this->assertSame(1, $delta);

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'votable_type' => $question->getMorphClass(),
            'votable_id' => $question->id,
            'value' => 1,
        ]);

        $this->assertSame(1, $question->fresh()->likes_count);
    }

    public function test_set_same_value_is_no_op(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['likes_count' => 0]);

        $this->service->set($user, $question, 1);
        $delta = $this->service->set($user, $question, 1);

        $this->assertSame(0, $delta);

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'votable_id' => $question->id,
            'value' => 1,
        ]);

        $this->assertSame(1, $question->fresh()->likes_count);
    }

    public function test_clear_removes_vote_and_decrements_likes_count(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['likes_count' => 0]);

        $this->service->set($user, $question, 1);
        $delta = $this->service->clear($user, $question);

        $this->assertSame(-1, $delta);

        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'votable_id' => $question->id,
        ]);

        $this->assertSame(0, $question->fresh()->likes_count);
    }

    public function test_clear_is_idempotent_when_no_vote_exists(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['likes_count' => 0]);

        $delta = $this->service->clear($user, $question);

        $this->assertSame(0, $delta);
        $this->assertSame(0, $question->fresh()->likes_count);
    }

    public function test_set_changes_like_to_dislike_updates_delta(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['likes_count' => 0]);

        $this->service->set($user, $question, 1);
        $delta = $this->service->set($user, $question, -1);

        $this->assertSame(-2, $delta);

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'votable_id' => $question->id,
            'value' => -1,
        ]);

        $this->assertSame(-1, $question->fresh()->likes_count);
    }

    public function test_set_dislike_on_comment_can_make_likes_count_negative(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create();
        $comment = new Comment([
            'body' => 'Test comment',
            'likes_count' => 0,
        ]);
        $comment->user()->associate($user);
        $comment->question()->associate($question);
        $comment->save();

        $delta = $this->service->set($user, $comment, -1);

        $this->assertSame(-1, $delta);
        $this->assertSame(-1, $comment->fresh()->likes_count);
        $this->assertDatabaseHas('votes', [
            'votable_id' => $comment->id,
            'value' => -1,
        ]);
    }

    public function test_vote_changed_not_dispatched_on_zero_delta(): void
    {
        Event::fake([VoteChanged::class]);

        $user = User::factory()->create();
        $question = Question::factory()->create(['likes_count' => 0]);

        $this->service->set($user, $question, 1);
        $this->service->set($user, $question, 1);

        Event::assertDispatchedTimes(VoteChanged::class, 1);
    }

    public function test_vote_changed_dispatched_when_delta_non_zero(): void
    {
        Event::fake([VoteChanged::class]);

        $user = User::factory()->create();
        $question = Question::factory()->create(['likes_count' => 0]);

        $this->service->set($user, $question, 1);

        Event::assertDispatched(VoteChanged::class, function (VoteChanged $event) use ($question) {
            return $event->delta === 1 && $event->votable->is($question);
        });
    }

    public function test_update_votable_likes_count_subscriber_is_not_queued(): void
    {
        $this->assertFalse(
            is_subclass_of(UpdateVotableLikesCountSubscriber::class, ShouldQueue::class),
        );
    }
}

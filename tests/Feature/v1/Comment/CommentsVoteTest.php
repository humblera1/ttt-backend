<?php

namespace Feature\v1\Comment;

use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommentsVoteTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('votes')->delete();
    }

    private function userWhoCanVote(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['vote-comment', 'view-any-comment']);

        return $user;
    }

    private function votableComment(): Comment
    {
        $author = User::factory()->create();
        $question = Question::factory()->create();

        $comment = new Comment([
            'body' => 'Test comment',
            'likes_count' => 0,
        ]);
        $comment->user()->associate($author);
        $comment->question()->associate($question);
        $comment->save();

        return $comment;
    }

    public function test_guest_cannot_vote_for_comment(): void
    {
        $comment = $this->votableComment();

        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), [
            'user_vote' => 'like',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_vote_for_comment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $comment = $this->votableComment();

        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), [
            'user_vote' => 'like',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_set_like_and_receives_delta(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $comment = $this->votableComment();

        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), [
            'user_vote' => 'like',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.delta', 1);
        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'votable_type' => $comment->getMorphClass(),
            'votable_id' => $comment->id,
            'value' => 1,
        ]);
        $this->assertSame(1, $comment->fresh()->likes_count);
    }

    public function test_setting_like_again_returns_zero_delta_on_comment(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $comment = $this->votableComment();

        $this->putJson(route('api.v1.comments.vote.update', $comment), ['user_vote' => 'like']);
        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), ['user_vote' => 'like']);

        $response->assertOk();
        $response->assertJsonPath('data.delta', 0);
        $this->assertSame(1, $comment->fresh()->likes_count);
    }

    public function test_user_can_switch_like_to_dislike_on_comment(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $comment = $this->votableComment();

        $this->putJson(route('api.v1.comments.vote.update', $comment), ['user_vote' => 'like']);
        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), ['user_vote' => 'dislike']);

        $response->assertOk();
        $response->assertJsonPath('data.delta', -2);
        $this->assertSame(-1, $comment->fresh()->likes_count);
    }

    public function test_user_can_clear_vote_on_comment(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $comment = $this->votableComment();

        $this->putJson(route('api.v1.comments.vote.update', $comment), ['user_vote' => 'like']);
        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), ['user_vote' => 'none']);

        $response->assertOk();
        $response->assertJsonPath('data.delta', -1);
        $this->assertSame(0, $comment->fresh()->likes_count);
        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'votable_id' => $comment->id,
        ]);
    }

    public function test_vote_for_soft_deleted_comment_returns_not_found(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $comment = $this->votableComment();
        $comment->delete();

        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), [
            'user_vote' => 'like',
        ]);

        $response->assertNotFound();
    }

    public function test_invalid_user_vote_returns_validation_error(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $comment = $this->votableComment();

        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), [
            'user_vote' => 'upvote',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['user_vote']);
    }
}

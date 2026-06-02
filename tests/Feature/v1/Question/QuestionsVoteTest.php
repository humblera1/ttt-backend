<?php

namespace Feature\v1\Question;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuestionsVoteTest extends TestCase
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
        $user->givePermissionTo(['vote-question', 'view-any-question']);

        return $user;
    }

    private function votableQuestion(): Question
    {
        return Question::factory()->common()->create([
            'likes_count' => 0,
            'is_premium' => false,
        ]);
    }

    public function test_guest_cannot_vote_for_question(): void
    {
        $question = $this->votableQuestion();

        $response = $this->putJson(route('api.v1.questions.vote.update', $question), [
            'user_vote' => 'like',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_vote_for_question(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $question = $this->votableQuestion();

        $response = $this->putJson(route('api.v1.questions.vote.update', $question), [
            'user_vote' => 'like',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_set_like_and_receives_delta(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $question = $this->votableQuestion();

        $response = $this->putJson(route('api.v1.questions.vote.update', $question), [
            'user_vote' => 'like',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.delta', 1);
        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'votable_type' => $question->getMorphClass(),
            'votable_id' => $question->id,
            'value' => 1,
        ]);
        $this->assertSame(1, $question->fresh()->likes_count);
    }

    public function test_setting_like_again_returns_zero_delta(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $question = $this->votableQuestion();

        $this->putJson(route('api.v1.questions.vote.update', $question), ['user_vote' => 'like']);
        $response = $this->putJson(route('api.v1.questions.vote.update', $question), ['user_vote' => 'like']);

        $response->assertOk();
        $response->assertJsonPath('data.delta', 0);
        $this->assertSame(1, $question->fresh()->likes_count);
        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'votable_id' => $question->id,
            'value' => 1,
        ]);
    }

    public function test_user_can_switch_like_to_dislike(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $question = $this->votableQuestion();

        $this->putJson(route('api.v1.questions.vote.update', $question), ['user_vote' => 'like']);
        $response = $this->putJson(route('api.v1.questions.vote.update', $question), ['user_vote' => 'dislike']);

        $response->assertOk();
        $response->assertJsonPath('data.delta', -2);
        $this->assertSame(-1, $question->fresh()->likes_count);
    }

    public function test_user_can_clear_vote_with_none(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $question = $this->votableQuestion();

        $this->putJson(route('api.v1.questions.vote.update', $question), ['user_vote' => 'like']);
        $response = $this->putJson(route('api.v1.questions.vote.update', $question), ['user_vote' => 'none']);

        $response->assertOk();
        $response->assertJsonPath('data.delta', -1);
        $this->assertSame(0, $question->fresh()->likes_count);
        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'votable_id' => $question->id,
        ]);
    }

    public function test_clear_vote_is_idempotent_when_no_vote_exists(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $question = $this->votableQuestion();

        $response = $this->putJson(route('api.v1.questions.vote.update', $question), ['user_vote' => 'none']);

        $response->assertOk();
        $response->assertJsonPath('data.delta', 0);
        $this->assertSame(0, $question->fresh()->likes_count);
    }

    public function test_invalid_user_vote_returns_validation_error(): void
    {
        $user = $this->userWhoCanVote();
        $this->actingAs($user);

        $question = $this->votableQuestion();

        $response = $this->putJson(route('api.v1.questions.vote.update', $question), [
            'user_vote' => 'upvote',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['user_vote']);
    }
}

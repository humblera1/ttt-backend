<?php

namespace Feature\v1\Vote;

use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LikesCountPersistenceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('votes')->delete();
    }

    public function test_question_likes_count_persisted_after_api_vote(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['vote-question', 'view-any-question']);
        $this->actingAs($user);

        $question = Question::factory()->common()->create([
            'likes_count' => 0,
            'is_premium' => false,
        ]);

        $response = $this->putJson(route('api.v1.questions.vote.update', $question), [
            'user_vote' => 'like',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.delta', 1);
        $this->assertSame(1, $question->fresh()->likes_count);
    }

    public function test_comment_likes_count_persisted_after_api_vote(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['vote-comment', 'view-any-comment']);
        $this->actingAs($user);

        $author = User::factory()->create();
        $question = Question::factory()->create();
        $comment = new Comment([
            'body' => 'Test comment',
            'likes_count' => 0,
        ]);
        $comment->user()->associate($author);
        $comment->question()->associate($question);
        $comment->save();

        $this->putJson(route('api.v1.comments.vote.update', $comment), ['user_vote' => 'like']);
        $response = $this->putJson(route('api.v1.comments.vote.update', $comment), [
            'user_vote' => 'dislike',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.delta', -2);
        $this->assertSame(-1, $comment->fresh()->likes_count);
    }

    public function test_question_factory_exposes_likes_count_attribute(): void
    {
        $question = Question::factory()->create(['likes_count' => -2]);

        $this->assertSame(-2, $question->likes_count);
        $this->assertSame(-2, $question->fresh()->likes_count);
    }
}

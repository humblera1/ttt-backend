<?php

namespace Feature\v1\Tag;

use App\Models\Tag;
use App\Models\User;
use App\Traits\Tests\ClearsTestTables;
use App\Traits\Tests\WithUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TagsTest extends TestCase
{
    use ClearsTestTables, DatabaseTransactions, WithUser;

    protected string $permission = 'view-any-tag';

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearTagsAndDependencies();
    }

    public function test_regular_user_cannot_access_tags_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson(route('api.v1.tags.list'));

        $response->assertForbidden();
    }

    public function test_endpoint_returns_only_active_tags(): void
    {
        $this->actingAs($this->getUser());

        $activeTags = Tag::factory()
            ->count(3)
            ->approved()
            ->create();

        $trashedTag = Tag::factory()->trashed()->create();

        $response = $this->getJson(route('api.v1.tags.list'));

        $response->assertOk();
        $response->assertJsonCount($activeTags->count(), 'data');

        $returnedIds = collect($response->json('data'))->pluck('id')->all();

        foreach ($activeTags as $tag) {
            $this->assertContains($tag->id, $returnedIds);
        }

        $this->assertNotContains($trashedTag->id, $returnedIds);
    }

    public function test_endpoint_returns_only_approved_tags(): void
    {
        $this->actingAs($this->getUser());

        $approvedTags = Tag::factory()
            ->count(3)
            ->approved()
            ->create();

        $pendingTag = Tag::factory()->pending()->create();
        $rejectedTag = Tag::factory()->rejected()->create();

        $response = $this->getJson(route('api.v1.tags.list'));

        $response->assertOk();
        $response->assertJsonCount($approvedTags->count(), 'data');

        $returnedIds = collect($response->json('data'))->pluck('id')->all();

        foreach ($approvedTags as $tag) {
            $this->assertContains($tag->id, $returnedIds);
        }

        $this->assertNotContains($pendingTag->id, $returnedIds);
        $this->assertNotContains($rejectedTag->id, $returnedIds);
    }

    public function test_user_can_search_approved_positions_by_name(): void
    {
        $this->actingAs($this->getUser());

        Tag::factory()
            ->count(3)
            ->approved()
            ->sequence(
                ['name' => 'Python'],
                ['name' => 'Elasticsearch'],
                ['name' => 'Yii2'],
            )->create();

        // Non-approved that should never match
        Tag::factory()
            ->pending()
            ->state(['name' => 'Postgres'])
            ->create();

        // Search: by "pyth"
        $response = $this->getJson(route('api.v1.tags.list', ['name' => 'pyth']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Python', $response->json('data')[0]['name']);

        // Search: by "search"
        $response = $this->getJson(route('api.v1.tags.list', ['name' => 'search']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Elasticsearch', $response->json('data')[0]['name']);

        // Search: by "yii"
        $response = $this->getJson(route('api.v1.tags.list', ['name' => 'yii']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Yii2', $response->json('data')[0]['name']);

        // Search: by "post"
        $response = $this->getJson(route('api.v1.tags.list', ['name' => 'post']));
        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}

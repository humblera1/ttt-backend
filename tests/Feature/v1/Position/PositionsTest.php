<?php

namespace Feature\v1\Position;

use App\Models\Position;
use App\Models\User;
use App\Traits\Tests\ClearsTestTables;
use App\Traits\Tests\WithUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PositionsTest extends TestCase
{
    use ClearsTestTables, DatabaseTransactions, WithUser;

    protected string $permission = 'view-any-position';

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearPositionsAndDependencies();
    }

    public function test_regular_user_cannot_access_positions_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson(route('api.v1.positions.list'));

        $response->assertForbidden();
    }

    public function test_endpoint_returns_only_active_positions(): void
    {
        $this->actingAs($this->getUser());

        $activePositions = Position::factory()
            ->count(3)
            ->approved()
            ->create();

        $trashedPosition = Position::factory()->trashed()->create();

        $response = $this->getJson(route('api.v1.positions.list'));

        $response->assertOk();
        $response->assertJsonCount($activePositions->count(), 'data');

        $returnedIds = collect($response->json('data'))->pluck('id')->all();

        foreach ($activePositions as $position) {
            $this->assertContains($position->id, $returnedIds);
        }

        $this->assertNotContains($trashedPosition->id, $returnedIds);
    }

    public function test_endpoint_returns_only_approved_positions(): void
    {
        $this->actingAs($this->getUser());

        $approvedPositions = Position::factory()
            ->count(3)
            ->approved()
            ->create();

        $pendingPosition = Position::factory()->pending()->create();
        $rejectedPosition = Position::factory()->rejected()->create();

        $response = $this->getJson(route('api.v1.positions.list'));

        $response->assertOk();

        $response->assertJsonCount($approvedPositions->count(), 'data');

        $returnedIds = collect($response->json('data'))->pluck('id')->all();

        foreach ($approvedPositions as $position) {
            $this->assertContains($position->id, $returnedIds);
        }

        $this->assertNotContains($pendingPosition->id, $returnedIds);
        $this->assertNotContains($rejectedPosition->id, $returnedIds);
    }

    public function test_user_can_search_approved_positions_by_name(): void
    {
        $this->actingAs($this->getUser());

        Position::factory()
            ->count(3)
            ->approved()
            ->sequence(
                ['name' => 'Developer'],
                ['name' => 'Lead Designer'],
                ['name' => 'QA Engineer'],
            )->create();

        // Non-approved that should never match
        Position::factory()
            ->pending()
            ->state(['name' => 'Pending Developer'])
            ->create();

        // Search: by "dev" => must return only approved "Developer"
        $response = $this->getJson(route('api.v1.positions.list', ['name' => 'dev']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Developer', $response->json('data')[0]['name']);

        // Search: by "lead" => only "Lead Designer"
        $response = $this->getJson(route('api.v1.positions.list', ['name' => 'lead']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Lead Designer', $response->json('data')[0]['name']);

        // Search: by "QA" => only "QA Engineer"
        $response = $this->getJson(route('api.v1.positions.list', ['name' => 'QA']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('QA Engineer', $response->json('data')[0]['name']);
    }
}

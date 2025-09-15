<?php

namespace Feature\v1\Position;

use App\Models\Position;
use DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PositionsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('positions')->delete();
    }

    public function test_endpoint_returns_only_approved_positions(): void
    {
        $approvedPositions = Position::factory()
            ->count(3)
            ->approved()
            ->create();

        Position::factory()->count(2)->pending();
        Position::factory()->count(2)->rejected();

        $response = $this->getJson(route('api.v1.positions.list'));

        $response->assertOk();

        $response->assertJsonCount($approvedPositions->count(), 'data');

        $returnedIds = collect($response->json('data'))->pluck('id')->all();
        foreach ($approvedPositions as $position) {
            $this->assertContains($position->id, $returnedIds);
        }
    }

    public function test_user_can_search_approved_positions_by_name(): void
    {
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

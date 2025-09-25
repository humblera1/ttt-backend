<?php

namespace Feature\v1\Company;

use App\Models\Company;
use App\Models\Position;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CompaniesTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('companies')->delete();
    }

    private function createUserWithPermission(string $permission): User
    {
        $user = User::factory()->create();

        $user->givePermissionTo($permission);

        return $user;
    }

    public function test_regular_user_cannot_access_companies_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson(route('api.v1.companies.list'));

        $response->assertForbidden();
    }

    public function test_premium_user_sees_only_active_companies(): void
    {
        $premiumUser = $this->createUserWithPermission('view-any-company');

        $this->actingAs($premiumUser);

        $activeCompanies = Company::factory()
            ->count(3)
            ->approved()
            ->create();

        $trashedCompany = Company::factory()->trashed()->create();

        $response = $this->getJson(route('api.v1.companies.list'));

        $response->assertOk();
        $response->assertJsonCount($activeCompanies->count(), 'data');

        $returnedIds = collect($response->json('data'))->pluck('id')->all();

        foreach ($activeCompanies as $company) {
            $this->assertContains($company->id, $returnedIds);
        }

        $this->assertNotContains($trashedCompany->id, $returnedIds);
    }

    public function test_premium_user_sees_only_approved_companies(): void
    {
        $premiumUser = $this->createUserWithPermission('view-any-company');

        $this->actingAs($premiumUser);

        $approvedCompanies = Company::factory()
            ->count(3)
            ->approved()
            ->create();

        $pendingCompany = Company::factory()->pending()->create();
        $rejectedCompany = Company::factory()->rejected()->create();

        $response = $this->getJson(route('api.v1.companies.list'));

        $response->assertOk();
        $response->assertJsonCount($approvedCompanies->count(), 'data');

        $returnedIds = collect($response->json('data'))->pluck('id')->all();

        foreach ($approvedCompanies as $company) {
            $this->assertContains($company->id, $returnedIds);
        }

        $this->assertNotContains($pendingCompany->id, $returnedIds);
        $this->assertNotContains($rejectedCompany->id, $returnedIds);
    }

    public function test_premium_user_can_search_companies_by_name(): void
    {
        $premiumUser = $this->createUserWithPermission('view-any-company');

        $this->actingAs($premiumUser);

        Company::factory()
            ->count(3)
            ->approved()
            ->sequence(
                ['name' => 'Рога и Копыта'],
                ['name' => 'LeadCorp'],
                ['name' => 'QA Solutions'],
            )->create();

        // Поиск по "рог"
        $response = $this->getJson(route('api.v1.companies.list', ['name' => 'рог']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Рога и Копыта', $response->json('data')[0]['name']);

        // По "lead"
        $response = $this->getJson(route('api.v1.companies.list', ['name' => 'lead']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('LeadCorp', $response->json('data')[0]['name']);

        // По "QA"
        $response = $this->getJson(route('api.v1.companies.list', ['name' => 'QA']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('QA Solutions', $response->json('data')[0]['name']);
    }
}

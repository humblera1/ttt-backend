<?php

namespace Feature\v1\Grade;

use App\Enums\Grade;
use App\Models\Position;
use App\Models\User;
use App\Traits\Tests\WithUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Grade as GradeModel;

class GradesTest extends TestCase
{
    use DatabaseTransactions, WithUser;

    protected string $permission = 'view-any-grade';

    public function test_regular_user_cannot_access_grades_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson(route('api.v1.grades.list'));

        $response->assertForbidden();
    }

    public function test_endpoint_returns_only_active_grades(): void
    {
        $this->actingAs($this->getUser());

        $gradeToDelete = GradeModel::where('name', Grade::Senior->value)->firstOrFail();
        $gradeToDelete->delete();

        $response = $this->getJson(route('api.v1.grades.list'));
        $response->assertOk();

        $activeGrades = GradeModel::query()->whereNull('deleted_at')->get();
        $response->assertJsonCount($activeGrades->count(), 'data');

        $returnedNames = collect($response->json('data'))->pluck('name')->all();

        foreach ($activeGrades as $grade) {
            $this->assertContains($grade->name, $returnedNames);
        }

        $this->assertNotContains($gradeToDelete->name, $returnedNames);
    }

    public function test_user_can_search_approved_positions_by_name(): void
    {
        $this->actingAs($this->getUser());

        $gradeToDelete = GradeModel::where('name', Grade::Senior->value)->firstOrFail();
        $gradeToDelete->delete();

        // Поиск по "jun"
        $response = $this->getJson(route('api.v1.grades.list', ['name' => 'jun']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Junior', $response->json('data')[0]['name']);

        // Поиск по "mid"
        $response = $this->getJson(route('api.v1.grades.list', ['name' => 'mid']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Middle', $response->json('data')[0]['name']);

        // Поиск по "lead"
        $response = $this->getJson(route('api.v1.grades.list', ['name' => 'lea']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertEquals('Lead', $response->json('data')[0]['name']);

        // Поиск по "sen"
        $response = $this->getJson(route('api.v1.grades.list', ['name' => 'sen']));
        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}

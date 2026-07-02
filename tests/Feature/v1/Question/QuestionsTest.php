<?php

namespace Feature\v1\Question;

use App\Models\Company;
use App\Models\Grade as GradeModel;
use App\Enums\Grade;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use App\Traits\Tests\ClearsTestTables;
use App\Traits\Tests\WithUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QuestionsTest extends TestCase
{
    use ClearsTestTables, DatabaseTransactions, WithUser;

    protected string $permission = 'view-any-question';

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearQuestionsAndDependencies();
    }

    public function test_regular_user_without_permission_cannot_access_questions_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson(route('api.v1.questions.list'));

        $response->assertForbidden();
    }

    public function test_user_with_permission_can_access_questions_list()
    {
        $this->actingAs($this->getUser());

        $questions = Question::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.questions.list'));

        $response->assertOk();
        $response->assertJsonCount($questions->count(), 'data');

        $returnedIds = collect($response->json('data'))->pluck('id')->all();

        foreach ($questions as $question) {
            $this->assertContains($question->id, $returnedIds);
        }
    }

    public function test_regular_user_cannot_see_premium_question_in_list()
    {
        $this->actingAs($this->getUser());

        Question::factory()->count(2)->create();
        $premiumQuestion = Question::factory()->premium()->create();

        $response = $this->getJson(route('api.v1.questions.list'));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        $responseCollection = collect($response->json('data'));

        $premiumData = $responseCollection->firstWhere('id', $premiumQuestion->id);

        $this->assertNotNull($premiumData, 'Premium question must be present in the response');
        $this->assertTrue($premiumData['is_premium']);
        $this->assertTrue($premiumData['locked']);

        $this->assertEqualsCanonicalizing(
            ['id', 'is_premium', 'locked'],
            array_keys($premiumData),
        );
    }

    public function test_regular_user_can_search_questions_by_name()
    {
        $this->actingAs($this->getUser());

        $matchingTitle = 'Unique Searchable Title';

        $questionWithTitle = Question::factory()->create(['title' => $matchingTitle]);
        $questionOther = Question::factory()->create(['title' => 'Another Title']);

        $response = $this->getJson(route('api.v1.questions.list', [
            'title' => 'searchable',
        ]));

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($questionWithTitle->id, $ids);
        $this->assertNotContains($questionOther->id, $ids);
    }

    public function test_regular_user_can_filter_questions_by_tag()
    {
        $this->actingAs($this->getUser());

        $tag = Tag::factory()->create();

        $questionWithTag = Question::factory()->create();
        $questionWithoutTag = Question::factory()->create();

        $questionWithTag->tags()->attach($tag);

        $response = $this->getJson(route('api.v1.questions.list', [
            'tagIds' => [$tag->id],
        ]));

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($questionWithTag->id, $ids);
        $this->assertNotContains($questionWithoutTag->id, $ids);
    }

    public function test_regular_user_can_filter_questions_by_grade()
    {
        $this->actingAs($this->getUser());

        $grade = GradeModel::where('name', Grade::Senior->value)->firstOrFail();

        $questionWithGrade = Question::factory()->create();
        $questionWithoutGrade = Question::factory()->create();

        $questionWithGrade->grades()->attach($grade);

        $response = $this->getJson(route('api.v1.questions.list', [
            'gradeIds' => [$grade->id],
        ]));

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($questionWithGrade->id, $ids);
        $this->assertNotContains($questionWithoutGrade->id, $ids);
    }

    public function test_regular_user_cannot_filter_questions_by_company()
    {
        $this->actingAs($this->getUser());

        $response = $this->getJson(route('api.v1.questions.list', [
            'companyIds' => [123, 456],
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['companyIds']);
    }

    public function test_premium_user_can_filter_questions_by_company()
    {
        $user = $this->getUser();

        $user->givePermissionTo('view-any-company');

        $this->actingAs($user);

        $company = Company::factory()->create();

        $questionWithCompany = Question::factory()->create();
        $questionWithoutCompany = Question::factory()->create();

        $questionWithCompany->companies()->attach($company);

        $response = $this->getJson(route('api.v1.questions.list', [
            'companyIds' => [$company->id],
        ]));

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($questionWithCompany->id, $ids);
        $this->assertNotContains($questionWithoutCompany->id, $ids);
    }

    public function test_regular_user_can_sort_questions_by_title_ascending_and_descending()
    {
        $this->actingAs($this->getUser());

        $expectedOrderAsc = ['Alpha', 'Bravo', 'Charlie'];
        $expectedOrderDesc = ['Charlie', 'Bravo', 'Alpha'];

        Question::factory()->common()->create(['title' => 'Alpha']);
        Question::factory()->common()->create(['title' => 'Bravo']);
        Question::factory()->common()->create(['title' => 'Charlie']);

        // Asc
        $response = $this->getJson(route('api.v1.questions.list', [
            'sort' => 'title',
        ]));

        $response->assertOk();

        $titles = collect($response->json('data'))->pluck('title')->values()->all();

        $this->assertEquals($expectedOrderAsc, $titles);

        // Desc
        $response = $this->getJson(route('api.v1.questions.list', [
            'sort' => '-title',
        ]));

        $response->assertOk();

        $titles = collect($response->json('data'))->pluck('title')->values()->all();

        $this->assertEquals($expectedOrderDesc, $titles);
    }

    public function test_regular_user_can_sort_questions_by_rating_ascending_and_descending()
    {
        $this->actingAs($this->getUser());

        $expectedOrderAsc = [10, 100, 1000];
        $expectedOrderDesc = [1000, 100, 10];

        Question::factory()->common()->create(['rating' => 10]);
        Question::factory()->common()->create(['rating' => 100]);
        Question::factory()->common()->create(['rating' => 1000]);

        // Asc
        $response = $this->getJson(route('api.v1.questions.list', [
            'sort' => 'rating',
        ]));

        $response->assertOk();

        $ratings = collect($response->json('data'))->pluck('rating')->values()->all();

        $this->assertEquals($expectedOrderAsc, $ratings);

        // Desc
        $response = $this->getJson(route('api.v1.questions.list', [
            'sort' => '-rating',
        ]));

        $response->assertOk();

        $ratings = collect($response->json('data'))->pluck('rating')->values()->all();

        $this->assertEquals($expectedOrderDesc, $ratings);
    }

    public function test_regular_user_can_sort_questions_by_published_at_ascending_and_descending()
    {
        $this->actingAs($this->getUser());

        $oldest = now()->subDays(3)->toDateTimeString();
        $middle = now()->subDays(2)->toDateTimeString();
        $newest = now()->toDateTimeString();

        $expectedOrderAsc = [$oldest, $middle, $newest];
        $expectedOrderDesc = [$newest, $middle, $oldest];

        Question::factory()->common()->create(['published_at' => $oldest]);
        Question::factory()->common()->create(['published_at' => $middle]);
        Question::factory()->common()->create(['published_at' => $newest]);

        // Asc
        $response = $this->getJson(route('api.v1.questions.list', [
            'sort' => 'published_at',
        ]));

        $response->assertOk();

        $dates = collect($response->json('data'))->pluck('published_at')->values()->all();

        $this->assertEquals($expectedOrderAsc, $dates);

        // Desc
        $response = $this->getJson(route('api.v1.questions.list', [
            'sort' => '-published_at',
        ]));

        $response->assertOk();

        $dates = collect($response->json('data'))->pluck('published_at')->values()->all();

        $this->assertEquals($expectedOrderDesc, $dates);
    }
}

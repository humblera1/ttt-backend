<?php

namespace Feature\v1\Question;

use App\Enums\Period;
use App\Models\Company;
use App\Models\Position;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use App\Traits\Tests\ClearsTestTables;
use App\Traits\Tests\WithUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QuestionsProposeTest extends TestCase
{
    use ClearsTestTables, DatabaseTransactions, WithUser;

    protected string $permission = 'propose-question';

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearQuestionDomainTables();
    }

    public function test_regular_user_without_permission_cannot_propose_questions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson(route('api.v1.questions.propose'));

        $response->assertForbidden();
    }

    public function test_user_with_permission_can_propose_minimal_question(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $payload = [
            'title' => 'Binary search basic',
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('questions', [
            'title' => 'Binary search basic',
            'user_id' => $user->id,
            'is_anonymous' => 0,
        ]);

        $question = Question::query()->where('title', 'Binary search basic')->first();
        $this->assertNotNull($question);
        $this->assertEquals($user->id, $question->user_id);
    }

    public function test_existing_tags_are_bound_without_duplicates(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        // create existing tags
        $tagA = Tag::query()->create(['name' => 'Algorithms']);
        $tagB = Tag::query()->create(['name' => 'Data Structures']);

        $payload = [
            'title' => 'Two pointers example',
            'tagsExisting' => [$tagA->id, $tagB->id],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Two pointers example')->firstOrFail();

        // Ensure exactly two tag relations created for this question
        $this->assertDatabaseCount('taggables', 2);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tagA->id,
            'taggables_id' => $question->id,
            'taggables_type' => Question::class,
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tagB->id,
            'taggables_id' => $question->id,
            'taggables_type' => Question::class,
        ]);
    }

    public function test_new_tags_are_created_and_bound_without_duplicates(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $payload = [
            'title' => 'Sliding window basics',
            'tagsNew' => ['Graphs', 'Dynamic Programming', 'graphs'],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Sliding window basics')->firstOrFail();

        // Expect 2 unique tags created (graphs normalized duplicates removed)
        $this->assertDatabaseCount('tags', 2);

        $graphTagId = Tag::query()->where('normalized_name', 'graphs')->value('id');
        $dpTagId = Tag::query()->where('normalized_name', 'dynamic programming')->value('id');

        $this->assertNotNull($graphTagId);
        $this->assertNotNull($dpTagId);

        // exactly two pivot rows for this question
        $this->assertDatabaseCount('taggables', 2);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $graphTagId,
            'taggables_id' => $question->id,
            'taggables_type' => Question::class,
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $dpTagId,
            'taggables_id' => $question->id,
            'taggables_type' => Question::class,
        ]);
    }

    public function test_new_tags_list_deduplicates_against_existing_records(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        // seed an existing tag; resolver should detect duplicate by normalized_name
        $existing = Tag::query()->create(['name' => '  graphs  ']);

        $payload = [
            'title' => 'Graph traversal',
            'tagsNew' => ['Graphs', 'Trees'],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Graph traversal')->firstOrFail();

        // Only two tags in DB: existing graphs and newly created trees
        $this->assertDatabaseHas('tags', [
            'id' => $existing->id,
            'normalized_name' => 'graphs',
        ]);

        $this->assertDatabaseHas('tags', [
            'normalized_name' => 'trees',
        ]);

        // Ensure there are exactly 2 records total in tags table
        $this->assertDatabaseCount('tags', 2);

        $graphsId = Tag::query()->where('normalized_name', 'graphs')->value('id');
        $treesId = Tag::query()->where('normalized_name', 'trees')->value('id');

        $this->assertEquals($existing->id, $graphsId);
        $this->assertNotNull($treesId);

        // Pivot should have exactly two rows for this question
        $this->assertDatabaseCount('taggables', 2);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $graphsId,
            'taggables_id' => $question->id,
            'taggables_type' => Question::class,
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $treesId,
            'taggables_id' => $question->id,
            'taggables_type' => Question::class,
        ]);
    }

    public function test_statistics_saved_when_met_in_real_interview_is_false(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $payload = [
            'title' => 'Edge cases awareness',
            'interview' => [
                'metInRealInterview' => false,
            ],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Edge cases awareness')->firstOrFail();

        $this->assertDatabaseCount('statistics', 1);
        $this->assertDatabaseHas('statistics', [
            'question_id' => $question->id,
            'user_id' => $user->id,
            'met_in_real_interview' => 0,
            'when_asked' => null,
            'company_id' => null,
            'position_id' => null,
        ]);
    }

    public function test_statistics_saved_with_existing_company_when_met_in_real_interview_is_true(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $company = Company::query()->create(['name' => 'Acme Inc']);
        $payload = [
            'title' => 'Behavioral question experience',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::LastMonth->value,
                'companyExisting' => $company->id,
            ],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Behavioral question experience')->firstOrFail();

        $this->assertDatabaseCount('statistics', 1);
        $this->assertDatabaseHas('statistics', [
            'question_id' => $question->id,
            'user_id' => $user->id,
            'met_in_real_interview' => 1,
            'when_asked' => Period::LastMonth->value,
            'company_id' => $company->id,
        ]);
    }

    public function test_statistics_saved_with_new_company_when_met_in_real_interview_is_true(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $payload = [
            'title' => 'System design interview recap',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::LastSixMonth->value,
                'companyNew' => '  Globex   ',
            ],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'System design interview recap')->firstOrFail();

        // company should be created via Resolver with normalized name
        $companyId = Company::query()->where('normalized_name', 'globex')->value('id');

        $this->assertNotNull($companyId);

        $this->assertDatabaseCount('statistics', 1);
        $this->assertDatabaseHas('statistics', [
            'question_id' => $question->id,
            'user_id' => $user->id,
            'met_in_real_interview' => 1,
            'when_asked' => Period::LastSixMonth->value,
            'company_id' => $companyId,
        ]);
    }

    public function test_new_company_deduplicates_against_existing_records(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $existing = Company::query()->create(['name' => 'Yandex']);

        $payload = [
            'title' => 'Optimize database indexing',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::LastSixMonth->value,
                'companyNew' => '  Yandex   ',
            ],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Optimize database indexing')->firstOrFail();

        $this->assertDatabaseHas('companies', [
            'id' => $existing->id,
            'normalized_name' => 'yandex',
        ]);

        $this->assertDatabaseCount('companies', 1);

        $yandexId = Company::query()->where('normalized_name', 'yandex')->value('id');

        $this->assertEquals($existing->id, $yandexId);

        $this->assertDatabaseCount('statistics', 1);
        $this->assertDatabaseHas('statistics', [
            'question_id' => $question->id,
            'user_id' => $user->id,
            'met_in_real_interview' => 1,
            'when_asked' => Period::LastSixMonth->value,
            'company_id' => $yandexId,
        ]);
    }

    public function test_statistics_saved_with_existing_position_when_met_in_real_interview_is_true(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $position = Position::query()->create(['name' => 'QA Engineer']);

        $payload = [
            'title' => 'Asynchronous event processing',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::LastYear->value,
                'positionExisting' => $position->id,
            ],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Asynchronous event processing')->firstOrFail();

        $this->assertDatabaseCount('statistics', 1);
        $this->assertDatabaseHas('statistics', [
            'question_id' => $question->id,
            'user_id' => $user->id,
            'met_in_real_interview' => 1,
            'when_asked' => Period::LastYear->value,
            'company_id' => null,
            'position_id' => $position->id,
        ]);
    }

    public function test_statistics_saved_with_new_position_when_met_in_real_interview_is_true(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $payload = [
            'title' => 'Preventing SQL injection',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::MoreThanYearAgo->value,
                'positionNew' => '  Lead QA  ',
            ],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Preventing SQL injection')->firstOrFail();

        $positionId = Position::query()->where('normalized_name', 'lead qa')->value('id');
        $this->assertNotNull($positionId);

        $this->assertDatabaseCount('statistics', 1);
        $this->assertDatabaseHas('statistics', [
            'question_id' => $question->id,
            'user_id' => $user->id,
            'met_in_real_interview' => 1,
            'when_asked' => Period::MoreThanYearAgo->value,
            'company_id' => null,
            'position_id' => $positionId,
        ]);
    }

    public function test_new_position_deduplicates_against_existing_records(): void
    {
        $user = $this->getUser();

        $this->actingAs($user);

        $existing = Position::query()->create(['name' => 'QA Engineer']);

        $payload = [
            'title' => 'Detecting circular dependencies',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::LastSixMonth->value,
                'positionNew' => '  QA Engineer   ',
            ],
        ];

        $response = $this->postJson(route('api.v1.questions.propose'), $payload);
        $response->assertCreated();

        $question = Question::query()->where('title', 'Detecting circular dependencies')->firstOrFail();

        $this->assertDatabaseHas('positions', [
            'id' => $existing->id,
            'normalized_name' => 'qa engineer',
        ]);

        $this->assertDatabaseCount('positions', 1);

        $qaId = Position::query()->where('normalized_name', 'qa engineer')->value('id');

        $this->assertEquals($existing->id, $qaId);

        $this->assertDatabaseCount('statistics', 1);
        $this->assertDatabaseHas('statistics', [
            'question_id' => $question->id,
            'user_id' => $user->id,
            'met_in_real_interview' => 1,
            'when_asked' => Period::LastSixMonth->value,
            'position_id' => $qaId,
        ]);
    }
}

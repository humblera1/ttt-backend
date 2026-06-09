<?php

namespace Feature\v1\Question;

use App\Enums\Period;
use App\Events\v1\Question\QuestionStatisticCreated;
use App\Listeners\v1\Question\UpdateQuestionMetInRealInterviewCountListener;
use App\Models\Company;
use App\Models\Position;
use App\Models\Question;
use App\Models\Statistic;
use App\Models\User;
use App\Traits\Tests\WithUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuestionMetInRealInterviewCountTest extends TestCase
{
    use DatabaseTransactions, WithUser;

    protected string $permission = 'propose-question';

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('statistics')->delete();
        DB::table('question_company_suggestions')->delete();
        DB::table('question_position_suggestions')->delete();
        DB::table('questions')->delete();
        DB::table('companies')->delete();
        DB::table('positions')->delete();
    }

    public function test_met_in_real_interview_statistic_increments_aggregate(): void
    {
        $user = $this->getUser();
        $this->actingAs($user);

        $company = Company::query()->create(['name' => 'Acme Inc']);

        $response = $this->postJson(route('api.v1.questions.propose'), [
            'title' => 'Aggregate increment test',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::LastMonth->value,
                'companyExisting' => $company->id,
            ],
        ]);

        $response->assertCreated();

        $question = Question::query()->where('title', 'Aggregate increment test')->firstOrFail();
        $this->dispatchMetStatisticCreatedEvent($question);

        $this->assertSame(1, $question->fresh()->met_in_real_interview_count);
    }

    public function test_statistic_with_met_false_does_not_increment_aggregate(): void
    {
        $user = $this->getUser();
        $this->actingAs($user);

        $response = $this->postJson(route('api.v1.questions.propose'), [
            'title' => 'Met false aggregate test',
            'interview' => [
                'metInRealInterview' => false,
            ],
        ]);

        $response->assertCreated();

        $question = Question::query()->where('title', 'Met false aggregate test')->firstOrFail();

        $this->assertSame(0, $question->fresh()->met_in_real_interview_count);
    }

    public function test_aggregate_matches_statistics_count(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['propose-question', 'send-feedback-question']);
        $this->actingAs($user);

        $companyA = Company::query()->create(['name' => 'Company A']);
        $companyB = Company::query()->create(['name' => 'Company B']);
        $positionA = Position::query()->create(['name' => 'Backend']);
        $positionB = Position::query()->create(['name' => 'Frontend']);

        $proposeResponse = $this->postJson(route('api.v1.questions.propose'), [
            'title' => 'Two met statistics',
            'interview' => [
                'metInRealInterview' => true,
                'whenAsked' => Period::LastMonth->value,
                'companyExisting' => $companyA->id,
                'positionExisting' => $positionA->id,
            ],
        ]);
        $proposeResponse->assertCreated();

        $question = Question::query()->where('title', 'Two met statistics')->firstOrFail();
        $this->dispatchMetStatisticCreatedEvent($question);

        $feedbackResponse = $this->postJson(route('api.v1.questions.feedback.submit', $question), [
            'metInRealInterview' => true,
            'whenAsked' => Period::LastSixMonth->value,
            'companyExisting' => $companyB->id,
            'positionExisting' => $positionB->id,
        ]);
        $feedbackResponse->assertCreated();
        $this->dispatchMetStatisticCreatedEvent($question);

        $expectedCount = Statistic::query()
            ->where('question_id', $question->id)
            ->where('met_in_real_interview', true)
            ->count();

        $this->assertSame(2, $expectedCount);
        $this->assertSame(2, $question->fresh()->met_in_real_interview_count);
    }

    public function test_listener_is_queued(): void
    {
        $this->assertTrue(
            is_subclass_of(UpdateQuestionMetInRealInterviewCountListener::class, ShouldQueue::class),
        );
    }

    /**
     * QuestionStatisticCreated uses ShouldDispatchAfterCommit; under DatabaseTransactions
     * the event does not fire until teardown — dispatch explicitly for listener coverage.
     */
    private function dispatchMetStatisticCreatedEvent(Question $question): void
    {
        $statistic = Statistic::query()
            ->where('question_id', $question->id)
            ->where('met_in_real_interview', true)
            ->latest('id')
            ->firstOrFail();

        app(UpdateQuestionMetInRealInterviewCountListener::class)
            ->handle(new QuestionStatisticCreated($statistic));
    }
}

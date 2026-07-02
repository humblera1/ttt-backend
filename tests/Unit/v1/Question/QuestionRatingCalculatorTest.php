<?php

namespace Tests\Unit\v1\Question;

use App\Enums\Settings\Section;
use App\Enums\Type;
use App\Models\Question;
use App\Models\Setting;
use App\Services\api\v1\Question\QuestionRatingCalculator;
use App\Services\api\v1\SettingsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QuestionRatingCalculatorTest extends TestCase
{
    use DatabaseTransactions;

    private QuestionRatingCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = app(QuestionRatingCalculator::class);
    }

    public function test_calculate_returns_zero_for_empty_aggregates(): void
    {
        $question = $this->questionWith([]);

        $this->assertSame(0, $this->calculator->calculate($question));
    }

    public function test_calculate_includes_likes_count_weighted(): void
    {
        $question = $this->questionWith(['likes_count' => 5]);

        $this->assertSame(5, $this->calculator->calculate($question));
    }

    public function test_calculate_includes_met_in_real_interview_count_weighted(): void
    {
        $question = $this->questionWith(['met_in_real_interview_count' => 2]);

        $this->assertSame(6, $this->calculator->calculate($question));
    }

    public function test_calculate_applies_log_views_and_comments(): void
    {
        $question = $this->questionWith([
            'views_count' => 9,
            'comments_count' => 4,
        ]);

        $expected = (int) round(
            log(10) * 0.3
            + log(5) * 0.7
        );

        $this->assertSame($expected, $this->calculator->calculate($question));
    }

    public function test_calculate_with_negative_likes_count(): void
    {
        $question = $this->questionWith(['likes_count' => -10]);

        $this->assertSame(-10, $this->calculator->calculate($question));
    }

    public function test_calculate_rounds_fractional_score(): void
    {
        $question = $this->questionWith(['views_count' => 1]);

        $this->assertSame(
            (int) round(log(2) * 0.3),
            $this->calculator->calculate($question),
        );
    }

    public function test_calculate_negative_score_is_not_clamped_to_zero(): void
    {
        $question = $this->questionWith(['likes_count' => -3]);

        $this->assertSame(-3, $this->calculator->calculate($question));
    }

    public function test_calculate_uses_setting_weights_with_defaults(): void
    {
        $question = $this->questionWith(['likes_count' => 5]);

        $this->assertSame(5, $this->calculator->calculate($question));

        Setting::query()->create([
            'section' => Section::RatingWeights->value,
            'key' => 'votes',
            'value' => '2.0',
            'label' => 'Votes weight test',
            'type' => Type::Float->value,
        ]);

        app(SettingsService::class)->clearCache();

        $this->assertSame(10, $this->calculator->calculate($question));
    }

    /**
     * @param  array<string, int>  $attributes
     */
    private function questionWith(array $attributes): Question
    {
        return Question::factory()->make(array_merge([
            'likes_count' => 0,
            'views_count' => 0,
            'comments_count' => 0,
            'met_in_real_interview_count' => 0,
        ], $attributes));
    }
}

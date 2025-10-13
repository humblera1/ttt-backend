<?php

namespace Database\Factories;

use App\Enums\Period;
use App\Models\Company;
use App\Models\Position;
use App\Models\Statistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Statistic>
 */
class StatisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'met_in_real_interview' => $this->faker->boolean(25),

            'company_id' => function (array $attributes) {
                return $attributes['met_in_real_interview'] ? Company::factory() : null;
            },

            'position_id' => function (array $attributes) {
                return $attributes['met_in_real_interview'] ? Position::factory() : null;
            },

            'when_asked' => function (array $attributes) {
                return $attributes['met_in_real_interview']
                    ? $this->faker->randomElement(Period::class)
                    : null;
            },
        ];
    }

    public function met(): static
    {
        return $this->state(function () {
            return [
                'met_in_real_interview' => true,
                'company_id' => Company::factory(),
                'position_id' => Position::factory(),
                'when_asked' => $this->faker->randomElement(Period::class),
            ];
        });
    }

    public function notMet(): static
    {
        return $this->state(fn () => [
            'met_in_real_interview' => false,
            'company_id' => null,
            'position_id' => null,
            'when_asked' => null,
        ]);
    }
}

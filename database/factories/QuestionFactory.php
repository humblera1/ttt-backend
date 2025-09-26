<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'answer' => $this->faker->paragraphs(3, true),
            'is_premium' => $this->faker->boolean(20),
            'views_count' => $this->faker->numberBetween(0, 20000),
            'likes_count' => $this->faker->numberBetween(-500, 3000),
            'comments_count' => $this->faker->numberBetween(0, 500),
            'rating' => $this->faker->numberBetween(0, 50000),
            'published_at' => $this->faker->dateTimeBetween('-2 years'),
            'updated_at' => now(),
        ];
    }

    public function premium(): static
    {
        return $this->state( fn () => [
            'is_premium' => true,
        ]);
    }

    public function common(): static
    {
        return $this->state( fn () => [
            'is_premium' => false,
        ]);
    }
}

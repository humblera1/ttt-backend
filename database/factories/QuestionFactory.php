<?php

namespace Database\Factories;

use App\Enums\QuestionRejectionReason;
use App\Enums\Status;
use App\Models\Question;
use App\Traits\Factories\WithStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    use WithStatus;

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
            'is_anonymous' => $this->faker->boolean(25),
            'status' => $this->faker->randomElement(Status::class),
            'views_count' => $this->faker->numberBetween(0, 20000),
            'likes_count' => $this->faker->numberBetween(-500, 3000),
            'comments_count' => $this->faker->numberBetween(0, 500),
            'rating' => $this->faker->numberBetween(0, 50000),
            'published_at' => $this->faker->dateTimeBetween('-2 years'),
            'updated_at' => now(),

            'rejection_reason'  => function (array $attributes) {
                return $attributes['status'] === Status::Rejected->value
                    ? $this->faker->randomElement(QuestionRejectionReason::class)
                    : null;
            },

            'rejection_comment' => function (array $attributes) {
                return $attributes['status'] === Status::Rejected->value
                    ? $this->faker->paragraph()
                    : null;
            },
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

<?php

namespace Database\Factories;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            'status' => $this->faker->randomElement(Status::class),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => Status::Pending->name,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => Status::Approved->name,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => Status::Rejected->name,
        ]);
    }
}

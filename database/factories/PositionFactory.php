<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Traits\Factories\WithStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
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
            'name' => $this->faker->unique()->jobTitle(),
            'status' => $this->faker->randomElement(Status::class),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class CapFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber() + 1,
            'value' => fake()->randomElement([100_000_00, 200_000_00]),
            'plan_id' => Plan::factory()->create(),
        ];
    }
}

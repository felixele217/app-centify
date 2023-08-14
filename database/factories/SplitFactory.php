<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

class SplitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber() + 1,
            'shared_percentage' => fake()->numberBetween(5, 80),
            'deal_id' => Deal::factory()->create(),
            'agent_id' => Agent::factory()->create(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentDealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber() + 1,
            'agent_id' => Agent::factory()->create(),
            'deal_id' => Deal::factory()->create(),
            'deal_percentage' => fake()->numberBetween(0, 100),
            'triggered_by' => null,
        ];
    }
}

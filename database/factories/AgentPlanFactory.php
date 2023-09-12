<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => uniqueIdWith6Digits(),
            'agent_id' => Agent::factory()->create(),
            'plan_id' => Plan::factory()->create(),
            'share_of_variable_pay' => 100,
        ];
    }
}

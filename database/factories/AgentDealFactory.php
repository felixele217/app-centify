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
            'id' => uniqueIdWith6Digits(),
            'agent_id' => Agent::factory(),
            'deal_id' => Deal::factory(),
            'deal_percentage' => 100,
            'triggered_by' => null,
        ];
    }
}

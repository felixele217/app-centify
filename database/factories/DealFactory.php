<?php

namespace Database\Factories;

use App\Enum\DealStatusEnum;
use App\Enum\IntegrationEnum;
use App\Models\Agent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'integration_type' => IntegrationEnum::PIPEDRIVE->value,
            'integration_deal_id' => 1,
            'title' => fake()->word(),
            'status' => fake()->randomElement(DealStatusEnum::cases())->value,
            'value' => 500000,
            'add_time' => Carbon::yesterday(),
            'agent_id' => Agent::factory()->create(),
        ];
    }
}

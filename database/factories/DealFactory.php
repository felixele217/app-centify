<?php

namespace Database\Factories;

use App\Enum\DealStatusEnum;
use App\Enum\IntegrationTypeEnum;
use App\Models\Agent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber(),
            'integration_type' => IntegrationTypeEnum::PIPEDRIVE->value,
            'integration_deal_id' => 1,
            'title' => fake()->word(),
            'status' => fake()->randomElement(DealStatusEnum::cases())->value,
            'owner_email' => fake()->email(),
            'value' => 500000,
            'add_time' => Carbon::yesterday(),
            'accepted_at' => null,
            'declined_at' => null,
            'agent_id' => Agent::factory()->create(),
        ];
    }
}

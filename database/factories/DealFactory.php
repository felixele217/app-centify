<?php

declare(strict_types=1);

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
            'id' => fake()->unique()->randomNumber() + 1,
            'integration_type' => IntegrationTypeEnum::PIPEDRIVE->value,
            'integration_deal_id' => 1,
            'title' => fake()->word(),
            'status' => DealStatusEnum::OPEN->value,
            'value' => 500000,
            'add_time' => Carbon::yesterday(),
            'won_time' => null,
            'accepted_at' => null,
            'demo_set_by_agent_id' => Agent::factory()->create(),
            'note' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'accepted_at' => Carbon::yesterday(),
        ]);
    }

    public function withAgentOfOrganization(int $organizationId): static
    {
        return $this->state(fn (array $attributes) => [
            'demo_set_by_agent_id' => Agent::factory()->create([
                'organization_id' => $organizationId,
            ]),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\DealStatusEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Models\AgentDeal;
use App\Models\Deal;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
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
            'add_time' => Carbon::now(),
            'won_time' => null,
            'note' => null,
        ];
    }

    public function won(Carbon|CarbonImmutable $wonAt = null): static
    {
        return $this->state(fn (array $attributes) => [
            'add_time' => $wonAt ?? Carbon::now(),
            'status' => DealStatusEnum::WON->value,
            'won_time' => $wonAt ?? Carbon::now(),
        ]);
    }

    public function withAgentDeal(int $agentId, TriggerEnum $trigger = null, Carbon|CarbonImmutable $accepted_at = null): static
    {
        return $this->afterCreating(function (Deal $deal) use ($agentId, $trigger, $accepted_at) {
            AgentDeal::factory()->create([
                'created_at' => Carbon::parse('-1 minute'),
                'agent_id' => $agentId,
                'deal_id' => $deal->id,
                'triggered_by' => $trigger->value,
                'accepted_at' => $accepted_at,
                'deal_percentage' => 100,
            ]);
        });
    }
}

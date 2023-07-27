<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use Worksome\RequestFactories\RequestFactory;

class StorePlanRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'start_date' => fake()->date(),
            'target_amount_per_month' => fake()->randomElement([200000, 400000]),
            'target_variable' => fake()->randomElement(TargetVariableEnum::cases())->value,
            'payout_frequency' => fake()->randomElement(PayoutFrequencyEnum::cases())->value,
            'assigned_agent_ids' => [],
        ];
    }
}

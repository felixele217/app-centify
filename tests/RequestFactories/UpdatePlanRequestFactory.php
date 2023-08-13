<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use App\Enum\PlanCycleEnum;
use App\Enum\TargetVariableEnum;
use Worksome\RequestFactories\RequestFactory;

class UpdatePlanRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'start_date' => fake()->date(),
            'target_amount_per_month' => fake()->randomElement([200000, 400000]),
            'target_variable' => fake()->randomElement(TargetVariableEnum::cases())->value,
            'plan_cycle' => fake()->randomElement(PlanCycleEnum::cases())->value,
            'assigned_agent_ids' => [],
        ];
    }
}

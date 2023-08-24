<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\PlanQuotaCommissionService;

it('returns the correct commission for a plan for a past time scope', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->active()
        ->hasAgents(1, [
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
            'trigger' => TriggerEnum::DEMO_SET_BY->value,
        ]);

    $agent = $plan->agents->first();

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SET_BY, DateHelper::dateInPreviousTimeScope($timeScope))
        ->create([
            'add_time' => DateHelper::dateInPreviousTimeScope($timeScope),
            'value' => $targetAmountPerMonth * $timeScope->monthCount(),
        ]);

    $expectedCommission = ($agent->on_target_earning - $agent->base_salary) / (12 / $timeScope->monthCount());

    expect((new PlanQuotaCommissionService($timeScope, DateHelper::dateInPreviousTimeScope($timeScope)->firstOfMonth()))->calculate($agent, $plan))->toBe(intval($expectedCommission));
})->with(TimeScopeEnum::cases());

<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\PlanQuotaCommissionService;
use Carbon\Carbon;

it('returns normal quota commission even if there is a cliff that was not met', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->active()
        ->hasCliff([
            'threshold_in_percent' => $cliffValue = 20,
        ])
        ->hasAgents(1, [
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    $cliffPercentage = (($cliffValue - 1) / 100);

    $agent = $plan->agents->first();

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => $targetAmountPerMonth * $timeScope->monthCount() * $cliffPercentage,
        ]);

    $expectedCommission = (($agent->variable_pay) / (12 / $timeScope->monthCount())) * $cliffPercentage;

    expect((new PlanQuotaCommissionService($timeScope))->calculate($agent, $plan, $timeScope))->toBe(intval($expectedCommission));
})->with(TimeScopeEnum::cases());

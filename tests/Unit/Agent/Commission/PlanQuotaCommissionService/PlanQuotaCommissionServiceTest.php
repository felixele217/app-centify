<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\PlanQuotaCommissionService;
use Carbon\Carbon;

it('returns the correct commission for a plan', function (TimeScopeEnum $timeScope, float $quotaAttainmentFactor) {
    $plan = Plan::factory()
        ->hasAgents(1, [
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'target_amount_per_month' => 10_000_00,
            'trigger' => TriggerEnum::DEMO_SET_BY->value,
        ]);

    $agent = $plan->agents->first();
    
    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => 10_000_00 * $quotaAttainmentFactor * $timeScope->monthCount(),
        ]);

    $expectedCommission = (($agent->on_target_earning - $agent->base_salary) / (12 / $timeScope->monthCount())) * $quotaAttainmentFactor;

    expect((new PlanQuotaCommissionService())->calculate($agent, $plan, $timeScope))->toBe(intval($expectedCommission));
})->with([
    [TimeScopeEnum::MONTHLY, 1],
    [TimeScopeEnum::QUARTERLY, 1],
    [TimeScopeEnum::ANNUALY, 1],

    [TimeScopeEnum::MONTHLY, 0.3],
    [TimeScopeEnum::QUARTERLY, 0.4],
    [TimeScopeEnum::ANNUALY, 1.3],
    [TimeScopeEnum::ANNUALY, 2.5],
    [TimeScopeEnum::ANNUALY, 0.7],
]);

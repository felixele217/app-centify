<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentPlan;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\PlanQuotaCommissionService;
use Carbon\Carbon;

it('returns the correct commission for a plan', function (TimeScopeEnum $timeScope, float $quotaAttainmentFactor) {
    $plan = Plan::factory()->active()
        ->hasAgents(1, [
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    $agent = $plan->agents->first();

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => $targetAmountPerMonth * $quotaAttainmentFactor * $timeScope->monthCount(),
        ]);

    $expectedCommission = (($agent->on_target_earning - $agent->base_salary) / (12 / $timeScope->monthCount())) * $quotaAttainmentFactor;

    expect((new PlanQuotaCommissionService($timeScope))->calculate($agent, $plan, $timeScope))->toBe(intval($expectedCommission));
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

it('returns the correct commission for a plan where the AgentPlan has a share of variable pay', function (TimeScopeEnum $timeScope, int $shareOfVariablePay) {
    $plan = Plan::factory()->active()
        ->create([
            'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    $agent = Agent::factory()->create([
        'base_salary' => 50_000_00,
        'on_target_earning' => 170_000_00,
    ]);

    AgentPlan::create([
        'agent_id' => $agent->id,
        'plan_id' => $plan->id,
        'share_of_variable_pay' => $shareOfVariablePay,
    ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => $targetAmountPerMonth * $timeScope->monthCount(),
        ]);

    $expectedCommission = (($agent->on_target_earning - $agent->base_salary) / (12 / $timeScope->monthCount())) * ($shareOfVariablePay / 100);

    expect((new PlanQuotaCommissionService($timeScope))->calculate($agent, $plan, $timeScope))->toBe(intval($expectedCommission));
})->with([
    [TimeScopeEnum::MONTHLY, 50],
    [TimeScopeEnum::QUARTERLY, 50],
    [TimeScopeEnum::ANNUALY, 50],

    [TimeScopeEnum::MONTHLY, 20],
    [TimeScopeEnum::QUARTERLY, 40],
    [TimeScopeEnum::ANNUALY, 60],
    [TimeScopeEnum::ANNUALY, 80],
    [TimeScopeEnum::ANNUALY, 100],
]);

<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentPlan;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\PlanQuotaCommissionService;
use Carbon\Carbon;

it('returns the quota commissions respective to the share of variable pay', function (TimeScopeEnum $timeScope) {
    $agent = Agent::factory()->create([
        'base_salary' => 50_000_00,
        'on_target_earning' => 170_000_00,
    ]);

    $plan = Plan::factory()->active()
        ->create([
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'value' => $plan->target_amount_per_month * $timeScope->monthCount(),
            'add_time' => Carbon::now(),
        ]);

    AgentPlan::factory()->create([
        'agent_id' => $agent->id,
        'plan_id' => $plan->id,
        'share_of_variable_pay' => $shareOfVariablePay = 30,
    ]);

    expect((new PlanQuotaCommissionService($timeScope))->calculate($agent, $plan))->toBe(
        intval(round(($shareOfVariablePay / 100) * (10_000_00 * $timeScope->monthCount())))
    );
})->with(TimeScopeEnum::cases());

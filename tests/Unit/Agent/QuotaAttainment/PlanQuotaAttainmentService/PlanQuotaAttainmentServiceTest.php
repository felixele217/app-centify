<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentPlan;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainment\PlanQuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment without incoorporating share of variable pay of the AgentPlan', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 1_000_00,
        'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
    ]);

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'value' => $targetAmountPerMonth,
        ]);

    $agent = Agent::find($agentId);

    AgentPlan::create([
        'agent_id' => $agent->id,
        'plan_id' => $plan->id,
        'share_of_variable_pay' => 30,
    ]);

    expect((new PlanQuotaAttainmentService($agent, $plan, $timeScope))->calculate())->toBe(floatval((1 / $timeScope->monthCount())));
})->with(TimeScopeEnum::cases());

<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\AgentPlan;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainment\TotalQuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment for the current scope for all deal participations of the agent', function (TimeScopeEnum $timeScope) {
    $sdrPlan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
    ]);

    $aePlan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEAL_WON->value,
    ]);

    $agent = Agent::factory()->create();

    $deals = Deal::factory(2)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->won(Carbon::now())
        ->create([
            'value' => 1_000_00,
        ]);

    foreach ($deals as $deal) {
        AgentDeal::factory()->create([
            'deal_id' => $deal->id,
            'agent_id' => $agent->id,
            'triggered_by' => TriggerEnum::DEAL_WON->value,
        ]);
    }

    $sdrPlan->agents()->attach($agent);
    $aePlan->agents()->attach($agent);

    expect((new TotalQuotaAttainmentService($agent, $timeScope))->calculate())->toBe(floatval((2 / $timeScope->monthCount())));
})->with([TimeScopeEnum::MONTHLY]);

it('returns 0 if the agent has no active plans in the scope', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->create([
        'start_date' => Carbon::now(),
    ]);

    Deal::factory(2)
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => 1_000_00,
        ]);

    $agent = Agent::find($agentId);

    $plan->agents()->attach($agent);

    expect((new TotalQuotaAttainmentService($agent, $timeScope, DateHelper::dateInPreviousTimeScope($timeScope)))->calculate())->toBe(floatval(0));
})->with(TimeScopeEnum::cases());

it('averages the quota attainments of multiple plans', function (TimeScopeEnum $timeScope, float $quotaAttainment) {
    $agent = Agent::factory()->create([
        'base_salary' => 50_000_00,
        'on_target_earning' => 170_000_00,
    ]);

    $sdrPlan = Plan::factory()->active()
        ->create([
            'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
        ]);

    $aePlan = Plan::factory()->active()
        ->create([
            'trigger' => TriggerEnum::DEAL_WON->value,
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'value' => $sdrPlan->target_amount_per_month * $timeScope->monthCount(),
            'add_time' => Carbon::now(),
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEAL_WON, Carbon::now())
        ->won(Carbon::now())
        ->create([
            'value' => $quotaAttainment * $aePlan->target_amount_per_month * $timeScope->monthCount(),
        ]);

    AgentPlan::factory()->create([
        'plan_id' => $aePlan->id,
        'agent_id' => $agent->id,
        'share_of_variable_pay' => 70,
    ]);

    AgentPlan::factory()->create([
        'plan_id' => $sdrPlan->id,
        'agent_id' => $agent->id,
        'share_of_variable_pay' => 30,
    ]);

    expect((new TotalQuotaAttainmentService($agent, $timeScope))->calculate())->toBe((1 + $quotaAttainment) / 2);
})->with([
    [TimeScopeEnum::MONTHLY, 0.5],
    [TimeScopeEnum::QUARTERLY, 0.5],
    [TimeScopeEnum::ANNUALY, 0.5],
    [TimeScopeEnum::MONTHLY, 0.2],
    [TimeScopeEnum::MONTHLY, 0.4],
    [TimeScopeEnum::MONTHLY, 0.6],
    [TimeScopeEnum::MONTHLY, 0.8],
    [TimeScopeEnum::MONTHLY, 1],
]);

it('returns 0 if the agent does not have a base_salary or on_target_earning', function (TimeScopeEnum $timeScope, int $baseSalary, int $onTargetEarning) {
    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create([
            'on_target_earning' => $onTargetEarning,
            'base_salary' => $baseSalary,
        ])->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now()->firstOfMonth(),
        ]);

    expect((new TotalQuotaAttainmentService(Agent::find($agentId), $timeScope, DateHelper::dateInPreviousTimeScope(TimeScopeEnum::MONTHLY)))->calculate())->toBe(floatval(0));
})->with([
    [TimeScopeEnum::MONTHLY, 10_000_00, 10_000_00],
    [TimeScopeEnum::QUARTERLY, 10_000_00, 10_000_00],
    [TimeScopeEnum::ANNUALY, 10_000_00, 10_000_00],
    [TimeScopeEnum::MONTHLY, 0, 10_000_00],
    [TimeScopeEnum::QUARTERLY, 0, 10_000_00],
    [TimeScopeEnum::ANNUALY, 0, 10_000_00],
    [TimescopeEnum::MONTHLY, 10_000_00, 0],
    [TimescopeEnum::QUARTERLY, 10_000_00, 0],
    [TimescopeEnum::ANNUALY, 10_000_00, 0],
]);

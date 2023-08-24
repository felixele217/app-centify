<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\TotalQuotaAttainmentService;
use Carbon\Carbon;

it('calculates the quota attainment for the current scope for all deal participations of the agent', function (TimeScopeEnum $timeScope) {
    $sdrPlan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEMO_SET_BY->value,
    ]);
    $aePlan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEAL_WON->value,
    ]);

    $deals = Deal::factory(2)
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->won(Carbon::now())
        ->create([
            'value' => 1_000_00,
        ]);

    foreach ($deals as $deal) {
        AgentDeal::factory()->create([
            'deal_id' => $deal->id,
            'agent_id' => $agentId,
            'triggered_by' => TriggerEnum::DEAL_WON->value,
        ]);
    }

    $agent = Agent::find($agentId);

    $sdrPlan->agents()->attach($agent);
    $aePlan->agents()->attach($agent);

    expect((new TotalQuotaAttainmentService($agent, $timeScope))->calculate())->toBe(floatval(2 * (2 / $timeScope->monthCount())));
})->with([TimeScopeEnum::MONTHLY]);

it('returns null if the agent has no active plans in the scope', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->create([
        'start_date' => Carbon::now(),
    ]);

    Deal::factory(2)
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => 1_000_00,
        ]);

    $agent = Agent::find($agentId);

    $plan->agents()->attach($agent);

    expect((new TotalQuotaAttainmentService($agent, $timeScope, DateHelper::dateInPreviousTimeScope($timeScope)))->calculate())->toBeNull();
})->with(TimeScopeEnum::cases());

it('returns null if the agent does not have a base_salary or on_target_earning', function (TimeScopeEnum $timeScope, int $baseSalary, int $onTargetEarning) {
    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create([
            'on_target_earning' => $onTargetEarning,
            'base_salary' => $baseSalary,
        ])->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->create([
            'add_time' => Carbon::now()->firstOfMonth(),
        ]);

    expect((new TotalQuotaAttainmentService(Agent::find($agentId), $timeScope, DateHelper::dateInPreviousTimeScope(TimeScopeEnum::MONTHLY)))->calculate())->toBeNull();
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

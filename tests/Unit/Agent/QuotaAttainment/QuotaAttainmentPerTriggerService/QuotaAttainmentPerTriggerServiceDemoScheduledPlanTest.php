<?php

use App\Enum\DealStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainmentPerTriggerService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('calculates the quota attainment for the current scope for all deals where demo was set by the agent', function (TimeScopeEnum $timeScope, Carbon $firstDateInScope, Carbon $lastDateInScope) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, $lastDateInScope)
        ->create([
            'add_time' => $lastDateInScope,
            'value' => 1_000_00,
        ]);

    Deal::factory()
        ->withAgentDeal($agentId, TriggerEnum::DEMO_SET_BY, $firstDateInScope)
        ->create([
            'add_time' => $firstDateInScope,
            'value' => 1_000_00,
        ]);

    $agent = Agent::find($agentId);

    $plan->agents()->attach($agent);

    expect((new QuotaAttainmentPerTriggerService($agent, TriggerEnum::DEMO_SET_BY, $timeScope))->calculate())->toBe(floatval(2 / $timeScope->monthCount()));
})->with([
    [TimeScopeEnum::MONTHLY, Carbon::now()->firstOfMonth(), Carbon::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()],
]);

it('does not use deals that were not scheduled by this agent', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->create([
        'trigger' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'won_time' => Carbon::now(),
            'status' => DealStatusEnum::WON->value,
        ]);

    AgentDeal::factory()->create([
        'deal_id' => $deal->id,
        'agent_id' => $agent = Agent::factory()->create(),
        'triggered_by' => TriggerEnum::DEAL_WON->value,
    ]);

    $plan->agents()->attach($agent);

    expect((new QuotaAttainmentPerTriggerService($agent, TriggerEnum::DEMO_SET_BY, $timeScope))->calculate())->toBe(floatval(0));
})->with(TimeScopeEnum::cases());

it('does not use deals that lie out of the current time scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    $plan = Plan::factory()->create([
        'trigger' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, $firstDateInScope->subDays(5))
        ->create([
            'add_time' => $firstDateInScope->subDays(5),
        ]);

    Deal::factory()
        ->withAgentDeal($agentId, TriggerEnum::DEMO_SET_BY, $lastDateInScope->addDays(5))
        ->create([
            'add_time' => $lastDateInScope->addDays(5),
        ]);
    $agent = Agent::find($agentId);
    $plan->agents()->attach($agent);

    expect((new QuotaAttainmentPerTriggerService($agent, TriggerEnum::DEMO_SET_BY, $timeScope))->calculate())->toBe(floatval(0));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

it('does not throw any errors when the agent does not have a base_salary, quota_attainment or plan', function ($baseSalary, $onTargetEarning) {
    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create([
            'on_target_earning' => $onTargetEarning,
            'base_salary' => $baseSalary,
        ])->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->create([
            'add_time' => Carbon::now()->firstOfMonth(),
        ]);

    expect((new QuotaAttainmentPerTriggerService(Agent::find($agentId), TriggerEnum::DEMO_SET_BY, TimeScopeEnum::MONTHLY))->calculate())->toBeNull();
})->with([
    [10_000_00, 10_000_00],
    [0, 10_000_00],
    [10_000_00, 0],
]);

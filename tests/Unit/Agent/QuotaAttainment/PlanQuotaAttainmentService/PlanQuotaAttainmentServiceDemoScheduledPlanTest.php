<?php

use App\Enum\DealStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\PlanQuotaAttainmentService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('calculates the quota attainment for the current scope for all deals where demo was set by the agent', function (TimeScopeEnum $timeScope, Carbon $firstDateInScope, Carbon $lastDateInScope) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
    ]);

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, $lastDateInScope)
        ->create([
            'add_time' => $lastDateInScope,
            'value' => 1_000_00,
        ]);

    Deal::factory()
        ->withAgentDeal($agentId, TriggerEnum::DEMO_SCHEDULED, $firstDateInScope)
        ->create([
            'add_time' => $firstDateInScope,
            'value' => 1_000_00,
        ]);

    $agent = Agent::find($agentId);

    $plan->agents()->attach($agent);

    expect((new PlanQuotaAttainmentService($agent, $plan, $timeScope))->calculate())->toBe(floatval(2 / $timeScope->monthCount()));
})->with([
    [TimeScopeEnum::MONTHLY, Carbon::now()->firstOfMonth(), Carbon::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()],
]);

it('does not use deals that were not scheduled by this agent', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->create([
        'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
    ]);

    $deal = Deal::factory()
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->won(Carbon::now())
        ->create();

    AgentDeal::factory()->create([
        'deal_id' => $deal->id,
        'agent_id' => $agent = Agent::factory()->create(),
        'triggered_by' => TriggerEnum::DEAL_WON->value,
    ]);

    $plan->agents()->attach($agent);

    expect((new PlanQuotaAttainmentService($agent, $plan, $timeScope))->calculate())->toBe(floatval(0));
})->with(TimeScopeEnum::cases());

it('does not use deals that lie out of the current time scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    $plan = Plan::factory()->create([
        'trigger' => TriggerEnum::DEMO_SCHEDULED->value,
    ]);

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SCHEDULED, $firstDateInScope->subDays(5))
        ->create([
            'add_time' => $firstDateInScope->subDays(5),
        ]);

    Deal::factory()
        ->withAgentDeal($agentId, TriggerEnum::DEMO_SCHEDULED, $lastDateInScope->addDays(5))
        ->create([
            'add_time' => $lastDateInScope->addDays(5),
        ]);
    $agent = Agent::find($agentId);
    $plan->agents()->attach($agent);

    expect((new PlanQuotaAttainmentService($agent, $plan, $timeScope))->calculate())->toBe(floatval(0));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

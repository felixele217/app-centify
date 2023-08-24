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

it('calculates the quota attainment for the current scope for all deals where deal was won by the agent', function (TimeScopeEnum $timeScope, Carbon $firstDateInScope, Carbon $lastDateInScope) {
    $plan = Plan::factory()->create([
        'target_amount_per_month' => 1_000_00,
        'trigger' => TriggerEnum::DEAL_WON->value,
    ]);

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEAL_WON, $lastDateInScope)
        ->won($lastDateInScope)
        ->create([
            'value' => 1_000_00,
        ]);

    Deal::factory()
        ->withAgentDeal($agentId, TriggerEnum::DEAL_WON, $firstDateInScope)
        ->won($firstDateInScope)
        ->create([
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

it('does not use deals that lie out of the current time scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    $plan = Plan::factory()->create([
        'trigger' => TriggerEnum::DEAL_WON->value,
    ]);

    Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEAL_WON, $firstDateInScope->subDays(5))
        ->won($firstDateInScope->subDays(5))
        ->create();

    Deal::factory()
        ->withAgentDeal($agentId, TriggerEnum::DEAL_WON, $lastDateInScope->addDays(5))
        ->won($lastDateInScope->addDays(5))
        ->create();

    $agent = Agent::find($agentId);
    $plan->agents()->attach($agent);

    expect((new PlanQuotaAttainmentService($agent, $plan, $timeScope))->calculate())->toBe(floatval(0));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

it('does not use deals that were not won by this agent', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->create([
        'trigger' => TriggerEnum::DEAL_WON->value,
    ]);

    $deal = Deal::factory()
        ->withAgentDeal($agentId = Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'won_time' => Carbon::now(),
            'status' => DealStatusEnum::WON->value,
        ]);

    AgentDeal::factory()->create([
        'deal_id' => $deal->id,
        'triggered_by' => TriggerEnum::DEAL_WON->value,
    ]);

    $agent = Agent::find($agentId);
    $plan->agents()->attach($agent);

    expect((new PlanQuotaAttainmentService($agent, $plan, $timeScope))->calculate())->toBe(floatval(0));
})->with(TimeScopeEnum::cases());

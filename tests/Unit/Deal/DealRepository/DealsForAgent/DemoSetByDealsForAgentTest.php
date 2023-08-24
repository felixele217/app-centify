<?php

use Carbon\Carbon;
use App\Models\Deal;
use App\Models\Plan;
use App\Models\Agent;
use App\Enum\TriggerEnum;
use App\Models\AgentDeal;
use App\Enum\TimeScopeEnum;
use Carbon\CarbonImmutable;
use App\Repositories\DealRepository;

beforeEach(function () {
    $plan = Plan::factory()->active()->create(['trigger' => TriggerEnum::DEMO_SET_BY->value]);

    $plan->agents()->attach($this->agent = Agent::factory()->create());
});

it('retrieves only deals where the add_time is inside of the time scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    Deal::factory(2)
        ->withAgentDeal($this->agent->id, TriggerEnum::DEMO_SET_BY, Carbon::now()->firstOfMonth())
        ->state(['add_time' => $firstDateInScope->subDays(10)])
        ->sequence(
            ['add_time' => $firstDateInScope],
            ['add_time' => $lastDateInScope],
        )->create();

    expect(DealRepository::dealsForAgent($this->agent, $timeScope))->toHaveCount(2);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

it('does not retrieve deals where the agent won the deal the demo but did not schedule the demo', function () {
    $deal = Deal::factory()
        ->won(Carbon::now())
        ->withAgentDeal($this->agent->id, TriggerEnum::DEAL_WON, Carbon::now())
        ->create();

    AgentDeal::factory()->create([
        'deal_id' => $deal->id,
        'triggered_by' => TriggerEnum::DEMO_SET_BY->value,
    ]);

    expect(DealRepository::dealsForAgent($this->agent, TimeScopeEnum::MONTHLY))->toHaveCount(0);
});

it('does not retrieve deals where add_time is outside of scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    Deal::factory(2)
        ->withAgentDeal($this->agent->id, TriggerEnum::DEMO_SET_BY, Carbon::now()->firstOfMonth())
        ->sequence(
            ['add_time' => $firstDateInScope->subDays(5)],
            ['add_time' => $lastDateInScope->addDays(5)],
        )->create();

    expect(DealRepository::dealsForAgent($this->agent, $timeScope))->toHaveCount(0);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

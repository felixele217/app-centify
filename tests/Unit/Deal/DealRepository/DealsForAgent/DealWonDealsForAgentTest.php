<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Repositories\DealRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $plan = Plan::factory()->active()->create(['trigger' => TriggerEnum::DEAL_WON->value]);

    $plan->agents()->attach($this->agent = Agent::factory()->create());
});

it('retrieves deals where the won_time is inside of the time scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    Deal::factory(2)
        ->withAgentDeal($this->agent->id, TriggerEnum::DEAL_WON, $firstDateInScope)
        ->state(['add_time' => $firstDateInScope->subDays(10)])
        ->sequence(
            ['won_time' => $firstDateInScope],
            ['won_time' => $lastDateInScope],
        )->create();

    expect(DealRepository::dealsForAgent($this->agent, $timeScope))->toHaveCount(2);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

it('does not retrieve deals where won_time is outside of scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    Deal::factory(2)
        ->withAgentDeal($this->agent->id, TriggerEnum::DEAL_WON, $firstDateInScope)
        ->sequence(
            ['won_time' => $firstDateInScope->subDays(5)],
            ['won_time' => $lastDateInScope->addDays(5)],
        )->create();

    expect(DealRepository::dealsForAgent($this->agent, $timeScope))->toHaveCount(0);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

it('does not retrieve deals where won_time is null', function (TimeScopeEnum $timeScope) {
    Deal::factory()
        ->withAgentDeal($this->agent->id, TriggerEnum::DEAL_WON, Carbon::now()->firstOfMonth())
        ->sequence(
            ['won_time' => null],
        )->create();

    expect(DealRepository::dealsForAgent($this->agent, $timeScope))->toHaveCount(0);
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);

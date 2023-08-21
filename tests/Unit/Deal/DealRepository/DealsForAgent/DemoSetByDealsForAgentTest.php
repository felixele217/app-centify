<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Repositories\DealRepository;
use Carbon\CarbonImmutable;


beforeEach(function () {
    $plan = Plan::factory()->active()->create(['trigger' => TriggerEnum::DEMO_SET_BY->value]);

    $plan->agents()->attach($this->agent = Agent::factory()->create());
});

it('retrieves only deals where the add_time is inside of the time scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    Deal::factory(2)->accepted()->state(['add_time' => $firstDateInScope->subDays(10)])->sequence(
            ['add_time' => $firstDateInScope],
            ['add_time' => $lastDateInScope],
    )->create([
        'agent_id' => $this->agent->id,
    ]);

    expect(DealRepository::dealsForAgent($this->agent, $timeScope))->toHaveCount(2);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

it('does not retrieve deals where add_time is outside of scope', function (TimeScopeEnum $timeScope, CarbonImmutable $firstDateInScope, CarbonImmutable $lastDateInScope) {
    Deal::factory(2)->accepted()->sequence(
        ['add_time' => $firstDateInScope->subDays(5)],
        ['add_time' => $lastDateInScope->addDays(5)],
    )->create([
        'agent_id' => $this->agent->id,
    ]);

    expect(DealRepository::dealsForAgent($this->agent, $timeScope))->toHaveCount(0);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth(), CarbonImmutable::now()->lastOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter(), CarbonImmutable::now()->lastOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear(), CarbonImmutable::now()->lastOfYear()],
]);

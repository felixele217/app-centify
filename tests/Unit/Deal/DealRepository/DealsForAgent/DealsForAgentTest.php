<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Repositories\DealRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('retrieves accepted deals', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->create());

    Deal::factory($acceptedWithoutRejectionsDealCount = 2)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SET_BY)
        ->accepted()
        ->create();

    Deal::factory($acceptedWithPriorRejectionsDealCount = 1)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SET_BY)
        ->accepted()
        ->hasRejections(1, [
            'is_permanent' => false,
            'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        ])
        ->create();

    $acceptedDealCount = $acceptedWithoutRejectionsDealCount + $acceptedWithPriorRejectionsDealCount;

    expect(DealRepository::dealsForAgent($agent, TimeScopeEnum::MONTHLY)->count())->toBe($acceptedDealCount);
});

it('does not retrieve unaccepted deals', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->create());

    $plan->agents()->attach($agent = Agent::factory()->create());

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SET_BY)
        ->create([
            'accepted_at' => null,
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SET_BY)
        ->hasRejections(1, [
            'is_permanent' => false,
        ])
        ->create([
            'accepted_at' => null,
        ]);

    expect(DealRepository::dealsForAgent($agent, TimeScopeEnum::MONTHLY)->count())->toBe(0);
});

//TODO handle the second accepted_at timestamp here properly
it('retrieves all deals where demo is set OR deal is won by the agent if he has plans with both triggers', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInScope) {
    $aePlan = Plan::factory()->active()->create(['trigger' => TriggerEnum::DEAL_WON->value]);
    $sdrPlan = Plan::factory()->active()->create(['trigger' => TriggerEnum::DEMO_SET_BY->value]);

    $agent = Agent::factory()->create();

    $agent->plans()->attach([$aePlan->id, $sdrPlan->id]);

    Deal::factory(2)
    ->withAgentDeal($agent->id, )
    ->accepted()
    ->sequence(
        [
            'add_time' => DateHelper::dateInPreviousTimeScope($timeScope),
            'won_time' => $dateInScope,
        ],
        [
            'add_time' => $dateInScope,
            'won_time' => null,
        ],
    )->create([
        'demo_set_by_agent_id' => $agent->id,
    ]);

    expect(DealRepository::dealsForAgent($agent, $timeScope))->toHaveCount(2);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()],
]);

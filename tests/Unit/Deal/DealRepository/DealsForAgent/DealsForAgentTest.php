<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
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
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create();

    Deal::factory($acceptedWithPriorRejectionsDealCount = 1)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
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
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, null)
        ->create();

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, null)
        ->hasRejections(1, [
            'is_permanent' => false,
        ])
        ->create();

    expect(DealRepository::dealsForAgent($agent, TimeScopeEnum::MONTHLY)->count())->toBe(0);
});

it('retrieves all deals where demo is set OR deal is won by the agent if he has plans with both triggers', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInScope) {
    $aePlan = Plan::factory()->active()->create(['trigger' => TriggerEnum::DEAL_WON->value]);
    $sdrPlan = Plan::factory()->active()->create(['trigger' => TriggerEnum::DEMO_SCHEDULED->value]);

    $agent = Agent::factory()->create();

    $agent->plans()->attach([$aePlan->id, $sdrPlan->id]);

    foreach ([TriggerEnum::DEMO_SCHEDULED, TriggerEnum::DEAL_WON] as $trigger) {
        Deal::factory()
            ->withAgentDeal($agent->id, $trigger, Carbon::now())
            ->won()
            ->create();
    }

    expect(DealRepository::dealsForAgent($agent, $timeScope))->toHaveCount(2);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()],
]);

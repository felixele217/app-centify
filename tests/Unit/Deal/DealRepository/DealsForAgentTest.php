<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Repositories\DealRepository;
use Carbon\Carbon;

it('retrieves only accepted deals', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($acceptedWithoutRejectionsDealCount = 2, [
        'accepted_at' => Carbon::now(),
    ])->create());

    Deal::factory($acceptedWithPriorRejectionsDealCount = 1)
        ->hasRejections(1, [
            'is_permanent' => false,
            'created_at' => Carbon::now()->firstOfMonth()->subDays(5),
        ])
        ->create([
            'agent_id' => $agent->id,
            'accepted_at' => Carbon::now(),
        ]);

    $acceptedDealCount = $acceptedWithoutRejectionsDealCount + $acceptedWithPriorRejectionsDealCount;

    expect(DealRepository::dealsForAgent($agent, TimeScopeEnum::MONTHLY)->count())->toBe($acceptedDealCount);
});

it('does not retrieve unaccepted deals', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(1, [
        'accepted_at' => null,
    ])->create());

    Deal::factory(3)
        ->hasRejections(1, [
            'is_permanent' => false,
        ])
        ->create([
            'agent_id' => $agent->id,
            'accepted_at' => null,
        ]);

    expect(DealRepository::dealsForAgent($agent, TimeScopeEnum::MONTHLY)->count())->toBe(0);
});

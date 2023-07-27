<?php

use Carbon\Carbon;
use App\Models\Deal;
use App\Models\Plan;
use App\Models\Agent;
use App\Enum\TimeScopeEnum;
use App\Repositories\DealRepository;

it('retrieves only accepted deals', function () {
    $plan = Plan::factory()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals($acceptedDealCount = 2, [
        'accepted_at' => Carbon::now(),
    ])->create());

    Deal::factory(3)->create([
        'agent_id' => $agent->id,
        'accepted_at' => null,
    ]);

    expect(DealRepository::dealsForAgent($agent, TimeScopeEnum::MONTHLY)->count())->toBe($acceptedDealCount);
});

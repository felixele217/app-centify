<?php

use App\Models\Agent;
use App\Models\Deal;
use App\Models\Split;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('returns only the splits whose deals are accepted', function () {
    Split::factory($splitCount = 2)->create([
        'agent_id' => $agent = Agent::factory()
            ->create(),
        'deal_id' => Deal::factory()->create([
            'accepted_at' => Carbon::now()->firstOfMonth(),
        ]),
    ]);

    expect($agent->splits()->acceptedDeals()->count())->toBe($splitCount);
});

it('returns only the splits whose deals are accepted before the passed date', function () {
    $cutoffDate = CarbonImmutable::parse('-9 days');

    Split::factory($splitCount = 2)->create([
        'agent_id' => $agent = Agent::factory()
            ->create(),
        'deal_id' => Deal::factory()->create([
            'accepted_at' => Carbon::parse('-10 days'),
        ]),
    ]);

    Split::factory(3)->create([
        'agent_id' => $agent->id,
        'deal_id' => Deal::factory()->create([
            'accepted_at' => Carbon::parse('-7 days'),
        ]),
    ]);

    expect($agent->splits()->acceptedDeals($cutoffDate)->count())->toBe($splitCount);
});

it('does not return the splits whose deals are not yet accepted', function () {
    Split::factory()->create([
        'agent_id' => $agent = Agent::factory()
            ->create(),
        'deal_id' => Deal::factory()->create([
            'accepted_at' => null,
        ]),
    ]);

    expect($agent->splits()->acceptedDeals()->count())->toBe(0);
});

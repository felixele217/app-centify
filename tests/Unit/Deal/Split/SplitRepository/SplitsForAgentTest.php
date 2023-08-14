<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Split;
use App\Repositories\SplitRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('returns all the splits of accepted deals', function (TimeScopeEnum $timeScope) {
    Split::factory($splitsOfAcceptedDealsCount = 2)->create([
        'agent_id' => $agent = Agent::factory()
            ->create(),
        'deal_id' => Deal::factory()->create([
            'accepted_at' => Carbon::now()->firstOfMonth(),
        ]),
    ]);

    expect(SplitRepository::splitsForAgent($agent, $timeScope)->count())->toBe($splitsOfAcceptedDealsCount);
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);

it('does not return splits of unaccepted deals', function (TimeScopeEnum $timeScope) {
    Split::factory()->create([
        'agent_id' => $agent = Agent::factory()
            ->create(),
        'deal_id' => Deal::factory()->create([
            'accepted_at' => null,
        ]),
    ]);

    expect(SplitRepository::splitsForAgent($agent, $timeScope)->count())->toBe(0);
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);

it('does not return splits of deals accepted after the passed date', function (TimeScopeEnum $timeScope) {
    $cutoffDate = CarbonImmutable::now()->firstOfMonth();

    Split::factory()->create([
        'agent_id' => $agent = Agent::factory()
            ->create(),
        'deal_id' => Deal::factory()->create([
            'accepted_at' => $cutoffDate->addDays(1),
        ]),
    ]);

    expect(SplitRepository::splitsForAgent($agent, $timeScope, $cutoffDate)->count())->toBe(0);
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);

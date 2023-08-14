<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Repositories\SplitRepository;
use Carbon\Carbon;

it('returns all the splits for the agent for the timescope', function () {
    $timeScope = TimeScopeEnum::MONTHLY;

    $agent = Agent::factory()->has(
        Deal::factory()->count(1)->state([
            'accepted_at' => Carbon::now()->firstOfMonth(),
            'add_time' => Carbon::now()->firstOfMonth(),
        ]))->create();

    $agent->deals()->first()->splits()->create([
        'agent_id' => $splitPartner = Agent::factory()
            ->has(
                Plan::factory()->active()->count(1)
            )
            ->ofOrganization($agent->organization_id)
            ->create(),
    ]);

    expect(SplitRepository::splitsForAgent($splitPartner, $timeScope)->first()->id)->toBe($agent->deals->first()->id);
})->todo();

<?php

use App\Enum\AgentStatusEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use Carbon\Carbon;

it('agent status is active when no paid leave for the current timeframe exists', function () {
    $agent = Agent::factory()->create();

    PaidLeave::factory()->create([
        'reason' => AgentStatusEnum::SICK->value,
        'agent_id' => $agent->id,
        'start_date' => Carbon::parse('-5 days'),
        'end_date' => Carbon::parse('-3 days'),
    ]);

    PaidLeave::factory()->create([
        'reason' => AgentStatusEnum::VACATION->value,
        'agent_id' => $agent->id,
        'start_date' => Carbon::parse('+2 days'),
        'end_date' => Carbon::parse('+5 days'),
    ]);

    expect($agent->status->value)->toBe(AgentStatusEnum::ACTIVE->value);
expect(5)->toBeEnum();
});

it('agent status is reason of paid leave when there is an active paid leave', function (string $reason) {
    $agent = Agent::factory()->create();

    PaidLeave::factory()->create([
        'reason' => $reason,
        'agent_id' => $agent->id,
        'start_date' => Carbon::parse('-2 days'),
        'end_date' => Carbon::parse('+2 days'),
    ]);

    expect($agent->status->value)->toBe($reason);
})->with([
    AgentStatusEnum::SICK->value,
    AgentStatusEnum::VACATION->value,
]);

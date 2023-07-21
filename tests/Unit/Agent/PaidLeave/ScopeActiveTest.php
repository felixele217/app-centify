<?php

use App\Enum\AgentStatusEnum;
use App\Models\Agent;
use App\Models\PaidLeave;
use App\Models\Plan;
use Carbon\Carbon;

it('returns the active paid leave', function () {
    $agent = Agent::factory()->create();

    $activePaidLeave = PaidLeave::factory()->create([
        'start_date' => $startDate = Carbon::yesterday(),
        'end_date' => $endDate = Carbon::parse('+1 week'),
        'reason' => AgentStatusEnum::VACATION->value,
        'agent_id' => $agent->id,
    ]);

    PaidLeave::factory()->create([
        'start_date' => Carbon::parse('-1 month'),
        'end_date' => Carbon::parse('-3 weeks'),
        'reason' => AgentStatusEnum::SICK->value,
        'agent_id' => $agent->id,
    ]);

    PaidLeave::factory()->create([
        'start_date' => Carbon::parse('+1 month'),
        'end_date' => Carbon::parse('+2 months'),
        'reason' => AgentStatusEnum::VACATION->value,
        'agent_id' => $agent->id,
    ]);

    $agent->plans()->attach(Plan::all());

    expect($agent->paidLeaves()->active()->count())->toBe(1);
    expect($agent->paidLeaves()->active()->first()->start_date->toDateString())->toBe($startDate->toDateString());
    expect($agent->paidLeaves()->active()->first()->end_date->toDateString())->toBe($endDate->toDateString());
});

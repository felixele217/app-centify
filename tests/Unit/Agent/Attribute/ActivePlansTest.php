<?php

use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use Carbon\Carbon;

it('computes the active plans correctly', function () {
    $agent = Agent::factory()->has(Plan::factory()->count(3)->sequence([
        'start_date' => Carbon::now()->firstOfYear(),
        'end_date' => Carbon::now()->lastOfYear(),
    ], [
        'start_date' => Carbon::now()->tomorrow(),
        'end_date' => Carbon::now()->parse('+2 days'),
    ]))->create();

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'value' => 3_333_33,
            'add_time' => Carbon::now(),
        ]);

    expect($agent->active_plans)->toHaveCount(2);
    expect($agent->active_plans->first())->toHaveKeys([
        'quota_attainment_in_percent',
        'quota_commission',
        'kicker_commission',
    ]);

    expect($agent->active_plans->first()['quota_attainment_in_percent'])->toBe(66.67);
});

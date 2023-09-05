<?php

use App\Models\Agent;
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

    expect($agent->active_plans)->toHaveCount(2);
    expect($agent->active_plans->first())->toHaveKeys([
        'quota_attainment',
        'quota_commission',
        'kicker_commission',
    ]);
});

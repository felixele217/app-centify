<?php

use App\Models\Agent;
use App\Models\Plan;
use Carbon\Carbon;

it('returns all plans whose start_date has started and end_date has not ended as active plans', function () {
    $agent = Agent::factory()->create();

    Plan::factory($activePlanWithoutEndDateCount = 1)->create([
        'start_date' => Carbon::yesterday(),
    ]);

    Plan::factory($activePlanWithEndDateCount = 1)->create([
        'start_date' => Carbon::yesterday(),
        'end_date' => Carbon::tomorrow(),
    ]);

    Plan::factory(3)->create([
        'start_date' => Carbon::tomorrow(),
    ]);

    Plan::factory(4)->create([
        'start_date' => Carbon::parse('-1 week'),
        'end_date' => Carbon::yesterday(),
    ]);

    $agent->plans()->attach(Plan::all());

    expect($agent->activePlans()->count())->toBe($activePlanWithEndDateCount + $activePlanWithoutEndDateCount);
});

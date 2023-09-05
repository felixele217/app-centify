<?php

use App\Models\Agent;
use App\Models\Plan;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('returns all plans whose start_date has started and end_date has not ended as active plans - sorted so the first entry is has the most recent start_date', function () {
    $agent = Agent::factory()->create();

    $latestPlan = Plan::factory($activePlanWithoutEndDateCount = 1)->create([
        'start_date' => Carbon::yesterday(),
    ]);

    Plan::factory($activePlanWithEndDateCount = 1)->create([
        'start_date' => Carbon::parse('-2 days'),
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

    expect($agent->plans()->active()->count())->toBe($activePlanWithEndDateCount + $activePlanWithoutEndDateCount);
    expect($agent->plans()->active()->first()->name)->toBe($latestPlan->first()->name);
});

it('returns plans whose start date equals the date in scope', function () {
    $agent = Agent::factory()->create();

    $plan = Plan::factory($planCount = 1)->create([
        'start_date' => $startDate = CarbonImmutable::yesterday(),
    ]);

    $agent->plans()->attach($plan);

    expect($agent->plans()->active($startDate)->count())->toBe($planCount);
});

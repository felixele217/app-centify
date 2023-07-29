<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('can calculate for past timescopes with the same plan', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInPastScope) {
    $plan = Plan::factory()->active()->has(
        Agent::factory()->hasDeals(2, [
            'accepted_at' => $dateInPastScope,
            'add_time' => $dateInPastScope,
            'value' => 5_000_00,
        ])->count(1)
    )->create([
        'target_amount_per_month' => 10_000_00,
    ]);

    expect((new QuotaAttainmentService($dateInPastScope))->calculate($plan->agents()->first(), $timeScope))->toBe(floatval(1 / $timeScope->monthCount()));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(10)],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(10)],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(10)],
]);

it('can calculate for past timescopes with a different plan', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInPastScope) {
    $plan = Plan::factory()->has(
        Agent::factory()->hasDeals(2, [
            'accepted_at' => $dateInPastScope,
            'add_time' => $dateInPastScope,
            'value' => 5_000_00,
        ])->count(1)
    )->create([
        'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
        'start_date' => $dateInPastScope->firstOfMonth(),
        'end_date' => $dateInPastScope->lastOfMonth(),
    ]);

    $plan->agents()->first()->plans()->attach(Plan::factory()->create([
        'target_amount_per_month' => $targetAmountPerMonth * 5,
        'start_date' => Carbon::now()->firstOfMonth(),
        'end_date' => Carbon::now()->lastOfMonth(),
    ]));

    expect((new QuotaAttainmentService($dateInPastScope))->calculate($plan->agents()->first(), $timeScope))->toBe(floatval(1 / $timeScope->monthCount()));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(10)],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(10)],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(10)],
]);

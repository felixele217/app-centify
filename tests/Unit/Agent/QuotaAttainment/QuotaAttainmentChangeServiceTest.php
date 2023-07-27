<?php

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainmentChangeService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('quota attainment change is calculated correctly', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInPreviousTimeScope, float $factorForThisScope) {
    $plan = Plan::factory()->active()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(2)->sequence([
            'accepted_at' => $dateInPreviousTimeScope,
            'add_time' => $dateInPreviousTimeScope,
            'value' => $targetAmountPerMonth * $timeScope->monthCount(),
        ], [
            'accepted_at' => Carbon::now(),
            'add_time' => Carbon::now(),
            'value' => $targetAmountPerMonth * $timeScope->monthCount() * $factorForThisScope,
        ]
        ))->create());

    expect((new QuotaAttainmentChangeService())->calculate($agent, $timeScope))->toBe(floatval((1 * $factorForThisScope) - 1));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 0.5],
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 1],
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 2],

    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 1],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(5), 1],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(5), 1],
]);

it('quota attainment change returns null when previous scope has no quota', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInPreviousTimeScope) {
    $plan = Plan::factory()->active()->create();

    $plan->agents()->attach($agent = Agent::factory()->hasDeals(2, [
        'accepted_at' => Carbon::now(),
        'add_time' => Carbon::now(),
    ])->create());

    expect((new QuotaAttainmentChangeService())->calculate($agent, $timeScope))->toBeNull();
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5)],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(5)],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(5)],
]);

it('quota attainment change does not return null for very small quotas in the previous scope', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInPreviousTimeScope) {
    $plan = Plan::factory()->active()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->has(
        Deal::factory()->count(2)->sequence([
            'accepted_at' => $dateInPreviousTimeScope,
            'add_time' => $dateInPreviousTimeScope,
            'value' => $targetAmountPerMonth * $timeScope->monthCount() * 0.001,
        ], [
            'accepted_at' => Carbon::now(),
            'add_time' => Carbon::now(),
            'value' => $targetAmountPerMonth * $timeScope->monthCount(),
        ]
        ))->create());

    expect((new QuotaAttainmentChangeService())->calculate($agent, $timeScope))->not()->toBeNull();
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5)],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(5)],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(5)],
]);

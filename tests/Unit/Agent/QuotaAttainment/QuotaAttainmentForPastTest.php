<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainmentService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('can calculate for past timescopes with the same plan', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInPastScope) {
    $plan = Plan::factory()->active()->create(['target_amount_per_month' => 10_000_00]);

    Deal::factory(2)
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, $dateInPastScope)
        ->create([
            'add_time' => $dateInPastScope,
            'value' => 5_000_00,
        ]);

    $plan->agents()->attach(Agent::first());

    expect((new QuotaAttainmentService($plan->agents()->first(), $timeScope, $dateInPastScope))->calculate())->toBe(floatval(1 / $timeScope->monthCount()));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(10)],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(10)],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(10)],
]);

it('can calculate for past timescopes with a different plan', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInPastScope) {
    $plan = Plan::factory()->active()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
        'start_date' => $dateInPastScope->firstOfMonth(),
        'end_date' => $dateInPastScope->lastOfMonth(),
    ]);

    Deal::factory(2)
        ->withAgentDeal(Agent::factory()->create()->id, TriggerEnum::DEMO_SET_BY, $dateInPastScope)
        ->create([
            'add_time' => $dateInPastScope,
            'value' => 5_000_00,
        ]);

    $plan->agents()->attach(Agent::first());

    $plan->agents()->first()->plans()->attach(Plan::factory()->create([
        'target_amount_per_month' => $targetAmountPerMonth * 5,
        'start_date' => Carbon::now()->firstOfMonth(),
        'end_date' => Carbon::now()->lastOfMonth(),
    ]));

    expect((new QuotaAttainmentService($plan->agents()->first(), $timeScope, $dateInPastScope))->calculate())->toBe(floatval(1 / $timeScope->monthCount()));
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(10)],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(10)],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(10)],
]);

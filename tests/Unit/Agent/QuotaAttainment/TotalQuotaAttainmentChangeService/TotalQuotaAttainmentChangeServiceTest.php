<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\QuotaAttainment\TotalQuotaAttainmentChangeService;
use App\Services\QuotaAttainment\TotalQuotaAttainmentService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

it('quota attainment change is calculated correctly for all kinds of deals with triggers', function (TriggerEnum $trigger, TimeScopeEnum $timeScope, CarbonImmutable $dateInPreviousTimeScope, float $factorForLastScope) {
    $plan = Plan::factory()->active()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
        'trigger' => $trigger->value,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->create());

    Deal::factory()
        ->withAgentDeal($agent->id, $trigger, Carbon::yesterday())
        ->won(Carbon::now())
        ->create([
            'value' => $targetAmountPerMonth * $timeScope->monthCount(),
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, $trigger, $dateInPreviousTimeScope)
        ->won($dateInPreviousTimeScope)
        ->create([
            'add_time' => $dateInPreviousTimeScope,
            'value' => $targetAmountPerMonth * $timeScope->monthCount() * $factorForLastScope,
        ]);

    expect((new TotalQuotaAttainmentChangeService())->calculate($agent, $timeScope))->toBe(floatval(1 - $factorForLastScope));
})->with([
    [TriggerEnum::DEMO_SCHEDULED, TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 0.3],
    [TriggerEnum::DEMO_SCHEDULED, TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 1],
    [TriggerEnum::DEMO_SCHEDULED, TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 1.6],
    [TriggerEnum::DEMO_SCHEDULED, TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(5), 1],
    [TriggerEnum::DEMO_SCHEDULED, TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(5), 1],

    [TriggerEnum::DEAL_WON, TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 0.3],
    [TriggerEnum::DEAL_WON, TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 1],
    [TriggerEnum::DEAL_WON, TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5), 1.6],

    [TriggerEnum::DEAL_WON, TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(5), 1],
    [TriggerEnum::DEAL_WON, TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(5), 1],
]);

it('quota attainment change returns null when previous scope had no active plans', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->create([
        'start_date' => Carbon::now()->firstOfMonth(),
        'end_date' => Carbon::now()->lastOfMonth(),
    ]);

    $plan->agents()->attach($agent = Agent::factory()->create());

    Deal::factory(2)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
        ]);

    expect((new TotalQuotaAttainmentChangeService())->calculate($agent, $timeScope))->toBeNull();
})->with([
    TimeScopeEnum::MONTHLY,
    TimeScopeEnum::QUARTERLY,
    TimeScopeEnum::ANNUALY,
]);

it('quota attainment change is correct when previous scope has quota and current does not', function (TimeScopeEnum $timeScope) {
    $plan = Plan::factory()->active()->create();

    $plan->agents()->attach($agent = Agent::factory()->create());

    Deal::factory(2)
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, $dateInPreviousTimeScope = DateHelper::dateInPreviousTimeScope(($timeScope)))
        ->create([
            'add_time' => $dateInPreviousTimeScope,
        ]);

    $expectedQuotaAttainmentChange = (new TotalQuotaAttainmentService($agent, $timeScope, $dateInPreviousTimeScope))->calculate();

    expect((new TotalQuotaAttainmentChangeService())->calculate($agent, $timeScope))->toBe(-$expectedQuotaAttainmentChange);
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5)],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(5)],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(5)],
]);

it('quota attainment change does not return null for very small quotas in the previous scope', function (TimeScopeEnum $timeScope, CarbonImmutable $dateInPreviousTimeScope) {
    $plan = Plan::factory()->active()->create([
        'target_amount_per_month' => $targetAmountPerMonth = 10_000_00,
    ]);

    $plan->agents()->attach($agent = Agent::factory()->create());

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, $dateInPreviousTimeScope)
        ->create([
            'add_time' => $dateInPreviousTimeScope,
            'value' => $targetAmountPerMonth * $timeScope->monthCount() * 0.001,
        ]);

    Deal::factory()
        ->withAgentDeal($agent->id, TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now(),
            'value' => $targetAmountPerMonth * $timeScope->monthCount(),
        ]);

    expect((new TotalQuotaAttainmentChangeService())->calculate($agent, $timeScope))->not()->toBeNull();
})->with([
    [TimeScopeEnum::MONTHLY, CarbonImmutable::now()->firstOfMonth()->subDays(5)],
    [TimeScopeEnum::QUARTERLY, CarbonImmutable::now()->firstOfQuarter()->subDays(5)],
    [TimeScopeEnum::ANNUALY, CarbonImmutable::now()->firstOfYear()->subDays(5)],
]);

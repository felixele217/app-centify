<?php

use App\Enum\AgentStatusEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Payout;
use App\Models\Plan;
use App\Services\Commission\PaidLeaveCommissionService;
use App\Services\Commission\TotalKickerCommissionService;
use App\Services\Commission\TotalQuotaCommissionService;
use App\Services\FreezePayoutsService;
use App\Services\PaidLeaveDaysService;
use App\Services\TotalQuotaAttainmentService;
use Carbon\Carbon;

it('freezes the current agent data in payouts', function (TimeScopeEnum $timeScope) {
    $organization = Organization::factory()->create();

    $agents = Agent::factory($agentCount = 2)->create([
        'organization_id' => $organization->id,
    ]);

    $plan = Plan::factory()
        ->active()
        ->create([
            'organization_id' => $organization->id,
        ]);

    $plan->agents()->attach($agents);

    Deal::factory()
        ->withAgentDeal(fake()->randomElement($agents->pluck('id')), TriggerEnum::DEMO_SCHEDULED, Carbon::now())
        ->create([
            'add_time' => Carbon::now()->firstOfMonth(),
        ]);

    (new FreezePayoutsService($organization, $timeScope))->freeze();

    expect(Payout::count())->toBe($agentCount);
    expect(floatval($agents->first()->payouts->first()->quota_attainment_percentage))->toBe($quotaAttainment = (new TotalQuotaAttainmentService($agents->first(), $timeScope))->calculate());
    expect($agents->first()->payouts->first()->commission_from_quota)->toBe((new TotalQuotaCommissionService($timeScope))->calculate($agents->first()));
    expect($agents->first()->payouts->first()->kicker_commission)->toBe((new TotalKickerCommissionService())->calculate($agents->first(), $timeScope) ?? 0);
    expect($agents->first()->payouts->first()->absence_commission)->toBe((new PaidLeaveCommissionService())->calculate($agents->first(), $timeScope));
    expect($agents->first()->payouts->first()->sick_days)->toBe(count((new PaidLeaveDaysService())->paidLeaveDays($agents->first(), $timeScope, AgentStatusEnum::SICK)));
    expect($agents->first()->payouts->first()->vacation_days)->toBe(count((new PaidLeaveDaysService())->paidLeaveDays($agents->first(), $timeScope, AgentStatusEnum::VACATION)));
})->with([
    TimeScopeEnum::MONTHLY,
]);

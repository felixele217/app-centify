<?php

use Carbon\Carbon;
use App\Models\Deal;
use App\Models\Plan;
use App\Models\Agent;
use App\Models\Payout;
use App\Enum\TimeScopeEnum;
use App\Models\Organization;
use App\Enum\AgentStatusEnum;
use App\Services\FreezePayoutsService;
use App\Services\PaidLeaveDaysService;
use App\Services\QuotaAttainmentService;
use App\Services\Commission\KickerCommissionService;
use App\Services\Commission\CommissionFromQuotaService;
use App\Services\Commission\PaidLeaveCommissionService;

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

    Deal::factory()->create([
        'add_time' => Carbon::now()->firstOfMonth(),
        'accepted_at' => Carbon::now(),
        'agent_id' => fake()->randomElement($agents->pluck('id')),
    ]);

    (new FreezePayoutsService($organization, $timeScope))->freeze();

    expect(Payout::count())->toBe($agentCount);
    expect(floatval($agents->first()->payouts->first()->quota_attainment_percentage))->toBe($quotaAttainment = (new QuotaAttainmentService())->calculate($agents->first(), $timeScope));
    expect($agents->first()->payouts->first()->commission_from_quota)->toBe((new CommissionFromQuotaService())->calculate($agents->first(), $timeScope, $quotaAttainment));
    expect($agents->first()->payouts->first()->kicker_commission)->toBe((new KickerCommissionService())->calculate($agents->first(), $timeScope, $quotaAttainment) ?? 0);
    expect($agents->first()->payouts->first()->absence_commission)->toBe((new PaidLeaveCommissionService())->calculate($agents->first(), $timeScope));
    expect($agents->first()->payouts->first()->sick_days)->toBe(count((new PaidLeaveDaysService())->paidLeaveDays($agents->first(), $timeScope, AgentStatusEnum::SICK)));
    expect($agents->first()->payouts->first()->vacation_days)->toBe(count((new PaidLeaveDaysService())->paidLeaveDays($agents->first(), $timeScope, AgentStatusEnum::VACATION)));
})->with([
    TimeScopeEnum::MONTHLY,
]);
<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\PlanKickerCommissionService;
use Carbon\Carbon;

it('does not incorporate the kicker if its target is not met because deals are outside of the time scope', function (TimeScopeEnum $timeScope, Carbon $dealAcceptedDate) {
    $admin = signInAdmin();

    $this->get(route('dashboard').'?time_scope='.$timeScope->value);

    $plan = Plan::factory()->active()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
            'payout_in_percent' => 25,
        ])
        ->hasAgents(1)
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    Deal::factory()
        ->withAgentDeal($plan->agents()->first()->id, TriggerEnum::DEMO_SCHEDULED, $dealAcceptedDate)
        ->create([
            'value' => 60_000_000,
            'add_time' => $dealAcceptedDate,
        ]);

    expect((new PlanKickerCommissionService($timeScope))->calculate($plan->agents()->first(), $plan, $plan->agents()->first()->quota_attainment_in_percent))->toBe(0);
})->with([
    [TimeScopeEnum::MONTHLY, Carbon::now()->firstOfMonth()->subDays(1)],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->firstOfQuarter()->subDays(1)],
    [TimeScopeEnum::ANNUALY, Carbon::now()->firstOfYear()->subDays(1)],

    [TimeScopeEnum::MONTHLY, Carbon::now()->lastOfMonth()->addDays(1)],
    [TimeScopeEnum::QUARTERLY, Carbon::now()->lastOfQuarter()->addDays(1)],
    [TimeScopeEnum::ANNUALY, Carbon::now()->lastOfYear()->addDays(1)],
]);

it('returns null for the kicker commission if the plan has no kicker', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->active()
        ->hasAgents(1, [
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
        ]);

    expect((new PlanKickerCommissionService(TimeScopeEnum::QUARTERLY))->calculate($plan->agents()->first(), $plan, 10))->toBeNull();
});

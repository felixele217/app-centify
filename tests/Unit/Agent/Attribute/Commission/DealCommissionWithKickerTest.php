<?php

use App\Enum\KickerTypeEnum;
use App\Enum\PayoutFrequencyEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Models\Deal;
use App\Models\Plan;
use App\Services\Commission\KickerCommissionService;
use Carbon\Carbon;

it('incorporates the kicker if its conditions are met', function (int $dealCount) {
    $admin = signInAdmin();

    $this->get(route('dashboard').'?time_scope='.TimeScopeEnum::MONTHLY->value);

    $plan = Plan::factory()
        ->hasKicker([
            'type' => KickerTypeEnum::SALARY_BASED_ONE_TIME->value,
            'salary_type' => SalaryTypeEnum::BASE_SALARY_MONTHLY->value,
            'threshold_in_percent' => 200,
            'payout_in_percent' => 25,
        ])
        ->hasAgents(1, [
            'base_salary' => 50_000_00,
            'on_target_earning' => 170_000_00,
        ])
        ->create([
            'organization_id' => $admin->organization->id,
            'creator_id' => $admin->id,
            'target_amount_per_month' => 10_000_00,
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::tomorrow(),
            'payout_frequency' => PayoutFrequencyEnum::MONTHLY->value,
        ]);

    Deal::factory($dealCount)->create([
        'agent_id' => $plan->agents()->first()->id,
        'value' => 20_000_00,
        'accepted_at' => Carbon::yesterday(),
    ]);

    $expectedKickerCommission = (50_000_00 / 12) * 0.25;

    expect((new KickerCommissionService())->calculate($plan->agents()->first()))->toBe(intval(round($expectedKickerCommission)));
})->with([
    1, 2,
]);

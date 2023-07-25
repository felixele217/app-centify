<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Http\Requests\StorePlanRequest;
use App\Models\Plan;

it('can store a plan with a kicker as an admin', function () {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'kicker' => [
            'type' => $type = fake()->randomElement(KickerTypeEnum::cases())->value,
            'threshold_in_percent' => $thresholdInPercent = 200,
            'payout_in_percent' => $payoutInPercent = 25,
            'salary_type' => $salaryType = fake()->randomElement(SalaryTypeEnum::cases())->value,
        ],
    ])->fake();

    $this->post(route('plans.store'))->assertRedirect(route('plans.index'));

    expect(Plan::first()->kicker->type->value)->toBe($type);
    expect(Plan::first()->kicker->threshold_in_percent)->toBe($thresholdInPercent / 100);
    expect(Plan::first()->kicker->payout_in_percent)->toBe($payoutInPercent / 100);
    expect(Plan::first()->kicker->salary_type->value)->toBe($salaryType);
});


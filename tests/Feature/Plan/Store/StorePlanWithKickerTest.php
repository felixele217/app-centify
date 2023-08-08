<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Http\Requests\StorePlanRequest;
use App\Models\Kicker;
use App\Models\Plan;

it('can store a plan with a kicker as an admin', function (TimeScopeEnum $timeScope) {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'kicker' => [
            'type' => $type = fake()->randomElement(KickerTypeEnum::cases())->value,
            'threshold_in_percent' => $thresholdInPercent = 200,
            'payout_in_percent' => $payoutInPercent = 25,
            'salary_type' => $salaryType = fake()->randomElement(SalaryTypeEnum::cases())->value,
            'time_scope' => $timeScope->value,
        ],
    ])->fake();

    $this->post(route('plans.store'))->assertRedirect(route('plans.index'));

    expect(Plan::first()->kicker->type->value)->toBe($type);
    expect(Plan::first()->kicker->threshold_in_percent)->toBe($thresholdInPercent / 100);
    expect(Plan::first()->kicker->payout_in_percent)->toBe($payoutInPercent / 100);
    expect(Plan::first()->kicker->salary_type->value)->toBe($salaryType);
    expect(Plan::first()->kicker->time_scope->value)->toBe($timeScope->value);
})->with(TimeScopeEnum::cases());

it('requires all kicker fields if at least one is specified', function (array $providedField, array $missingFields) {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'kicker' => $providedField,
    ])->fake();

    $this->post(route('plans.store'))->assertInvalid($missingFields);
})->with([
    [
        [
            'type' => fake()->randomElement(KickerTypeEnum::cases())->value,
        ],
        [
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
        ],
    ],
    [
        [
            'threshold_in_percent' => 200,
        ],
        [
            'kicker.type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
        ],
    ],
    [
        [
            'payout_in_percent' => 25,
        ],
        [
            'kicker.type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
        ],
    ],
    [
        [
            'salary_type' => fake()->randomElement(SalaryTypeEnum::cases())->value,
        ],
        [
            'kicker.type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
        ],
    ],
]);

it('does not store a kicker when an array with empty values is sent', function () {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'kicker' => [
            'salary_type' => null,
            'type' => null,
            'threshold_in_percent' => 0,
            'payout_in_percent' => 0,
        ],
    ])->fake();

    $this->post(route('plans.store'))->assertRedirect();

    expect(Kicker::count())->toBe(0);
});

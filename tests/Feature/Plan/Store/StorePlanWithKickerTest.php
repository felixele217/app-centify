<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Http\Requests\StorePlanRequest;
use App\Models\Kicker;
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
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker.',
        ],
    ],
    [
        [
            'threshold_in_percent' => 200,
        ],
        [
            'kicker.type' => 'Please specify all fields for the Kicker.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker.',
        ],
    ],
    [
        [
            'payout_in_percent' => 25,
        ],
        [
            'kicker.type' => 'Please specify all fields for the Kicker.',
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker.',
        ],
    ],
    [
        [
            'salary_type' => fake()->randomElement(SalaryTypeEnum::cases())->value,
        ],
        [
            'kicker.type' => 'Please specify all fields for the Kicker.',
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker.',
        ],
    ],
]);

it('does not throw validation errors if you send 0 as values in either of the percent fields', function (?int $thresholdInPercent, ?int $payoutInPercent) {
    signInAdmin();

    StorePlanRequest::factory()->state([
        'kicker' => [
            'salary_type' => null,
            'type' => null,
            'threshold_in_percent' => $thresholdInPercent,
            'payout_in_percent' => $payoutInPercent,
        ],
    ])->fake();

    $this->post(route('plans.store'))->assertValid();
})->with([
    [0, null],
    [null, 0],
    [0, 0],
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

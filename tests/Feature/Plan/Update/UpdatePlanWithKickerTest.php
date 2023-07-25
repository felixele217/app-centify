<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;

beforeEach(function () {
    $this->admin = signInAdmin();
});

it('can update a plan with a kicker as an admin', function (Plan $plan) {
    UpdatePlanRequest::factory()->state([
        'kicker' => [
            'type' => $type = fake()->randomElement(KickerTypeEnum::cases())->value,
            'threshold_in_percent' => $thresholdInPercent = 200,
            'payout_in_percent' => $payoutInPercent = 25,
            'salary_type' => $salaryType = fake()->randomElement(SalaryTypeEnum::cases())->value,
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertRedirect(route('plans.index'));

    expect(Plan::first()->kicker->type->value)->toBe($type);
    expect(Plan::first()->kicker->threshold_in_percent)->toBe($thresholdInPercent / 100);
    expect(Plan::first()->kicker->payout_in_percent)->toBe($payoutInPercent / 100);
    expect(Plan::first()->kicker->salary_type->value)->toBe($salaryType);
})->with([
    fn () => Plan::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
    fn () => Plan::factory()->hasKicker()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
]);

it('requires all kicker fields if at least one is specified', function (array $providedField, array $missingFields) {
    $plan = Plan::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'kicker' => $providedField,
    ])->fake();

    $this->put(route('plans.update', $plan))->assertInvalid($missingFields);
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

it ('can update a plan with removing the kicker as an admin', function () {

})->todo();

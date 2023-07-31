<?php

use App\Enum\KickerTypeEnum;
use App\Enum\SalaryTypeEnum;
use App\Enum\TimeScopeEnum;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Kicker;
use App\Models\Plan;

beforeEach(function () {
    $this->admin = signInAdmin();

    Plan::factory()->create();
});

it('can update a plan with a kicker as an admin', function (Plan $plan) {
    UpdatePlanRequest::factory()->state([
        'kicker' => [
            'type' => $type = fake()->randomElement(KickerTypeEnum::cases())->value,
            'threshold_in_percent' => $thresholdInPercent = 200,
            'payout_in_percent' => $payoutInPercent = 25,
            'salary_type' => $salaryType = fake()->randomElement(SalaryTypeEnum::cases())->value,
            'time_scope' => $timeScope = fake()->randomElement(TimeScopeEnum::cases())->value,
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertRedirect(route('plans.index'));

    expect(Kicker::wherePlanId($plan->id)->count())->toBe(1);
    expect($plan->kicker->type->value)->toBe($type);
    expect($plan->kicker->threshold_in_percent)->toBe($thresholdInPercent / 100);
    expect($plan->kicker->payout_in_percent)->toBe($payoutInPercent / 100);
    expect($plan->kicker->salary_type->value)->toBe($salaryType);
    expect($plan->kicker->time_scope->value)->toBe($timeScope);
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
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.time_scope' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
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
            'kicker.time_scope' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
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
            'kicker.time_scope' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
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
            'kicker.time_scope' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
        ],
    ],
    [
        [
            'time_scope' => fake()->randomElement(TimeScopeEnum::cases())->value,
        ],
        [
            'kicker.type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.threshold_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.payout_in_percent' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
            'kicker.salary_type' => 'Please specify all fields for the Kicker if you want to have one in your plan.',
        ],
    ]
]);

it('does not throw validation errors if you send 0 as values in either of the percent fields', function (?int $thresholdInPercent, ?int $payoutInPercent) {
    $plan = Plan::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'kicker' => [
            'salary_type' => null,
            'type' => null,
            'threshold_in_percent' => $thresholdInPercent,
            'payout_in_percent' => $payoutInPercent,
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertValid();
})->with([
    [0, null],
    [null, 0],
    [0, 0],
]);

it('does not update a kicker when an array with empty values is sent', function () {
    $plan = Plan::factory()->hasKicker()->create([
        'organization_id' => $this->admin->organization->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'kicker' => [
            'salary_type' => null,
            'type' => null,
            'threshold_in_percent' => 0,
            'payout_in_percent' => 0,
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertRedirect();

    expect($plan->kicker->type)->not()->toBeNull();
    expect($plan->kicker->salary_type)->not()->toBeNull();
    expect($plan->kicker->threshold_in_percent)->not()->toBeNull();
    expect($plan->kicker->payout_in_percent)->not()->toBeNull();
});

it('can update a plan with removing the kicker as an admin', function () {

})->todo();

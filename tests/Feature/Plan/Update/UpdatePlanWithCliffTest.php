<?php

use App\Enum\TimeScopeEnum;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Cliff;
use App\Models\Plan;

beforeEach(function () {
    $this->admin = signInAdmin();

    Plan::factory()->create();
});

it('can update a plan with a cliff as an admin', function (Plan $plan) {
    UpdatePlanRequest::factory()->state([
        'cliff' => [
            'threshold_in_percent' => $cliffPercentage = 10,
            'time_scope' => $timeScope = fake()->randomElement(TimeScopeEnum::cases())->value,
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertRedirect(route('plans.index'));

    expect(Cliff::wherePlanId($plan->id)->count())->toBe(1);
    expect($plan->fresh()->cliff->threshold_in_percent)->toBe($cliffPercentage / 100);
    expect($plan->cliff->time_scope->value)->toBe($timeScope);
})->with([
    fn () => Plan::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
    fn () => Plan::factory()->hasCliff()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
]);

it('can update a plan with removing the cliff as an admin', function () {

})->todo();

it('requires all cliff fields if at least one is specified', function (array $providedField, array $missingFields) {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'cliff' => $providedField,
    ])->fake();

    $this->put(route('plans.update', $plan))->assertInvalid($missingFields);
})->with([
    [
        [
            'threshold_in_percent' => 25,
        ],
        [
            'cliff.time_scope' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
        ],
    ],
    [
        [
            'time_scope' => TimeScopeEnum::MONTHLY->value,
        ],
        [
            'cliff.threshold_in_percent' => 'Please specify all fields for the Cliff if you want to have one in your plan.',
        ],
    ],
]);

it('does not throw validation errors if you send 0 as values in either of the percent fields', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'cliff' => [
            'threshold_in_percent' => 0,
            'time_scope' => null,
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertValid();
});

it('does not store a cliff when an array with empty values is sent', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'cliff' => [
            'threshold_in_percent' => 0,
            'time_scope' => null,
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertValid();

    expect(Cliff::count())->toBe(0);
});

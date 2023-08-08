<?php

use App\Http\Requests\UpdatePlanRequest;
use App\Models\Cap;
use App\Models\Plan;

beforeEach(function () {
    $this->admin = signInAdmin();

    Plan::factory()->create();
});

it('can update a plan with a cap as an admin', function (Plan $plan) {
    UpdatePlanRequest::factory()->state([
        'cap' => $cap = 100_000_00,
    ])->fake();

    $this->put(route('plans.update', $plan))->assertRedirect(route('plans.index'));

    expect(Cap::wherePlanId($plan->id)->count())->toBe(1);
    expect($plan->cap->value)->toBe($cap);
})->with([
    fn () => Plan::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
    fn () => Plan::factory()->hasCap()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
]);

it('does not throw validation error if you send cap of 0', function () {
    $plan = Plan::factory()->hasCap()->create([
        'organization_id' => $this->admin->organization->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'cap' => null,
    ])->fake();

    $this->put(route('plans.update', $plan))->assertValid();
});

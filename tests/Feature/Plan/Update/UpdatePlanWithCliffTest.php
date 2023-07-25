<?php

use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;

beforeEach(function () {
    $this->admin = signInAdmin();
});

it('can update a plan with a cliff as an admin', function (Plan $plan) {
    UpdatePlanRequest::factory()->state([
        'cliff_threshold_in_percent' => $cliffPercentage = 20,
    ])->fake();

    $this->put(route('plans.update', $plan))->assertRedirect(route('plans.index'));

    expect($plan->fresh()->cliff->threshold_in_percent)->toBe($cliffPercentage / 100);
})->with([
    fn () => Plan::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
    fn () => Plan::factory()->hasCliff()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
]);

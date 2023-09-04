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
    expect($plan->fresh()->cliff->threshold_factor)->toBe($cliffPercentage / 100);
    expect($plan->cliff->time_scope->value)->toBe($timeScope);
})->with([
    fn () => Plan::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
    fn () => Plan::factory()->hasCliff()->create([
        'organization_id' => $this->admin->organization->id,
    ]),
]);

it('does not store a cliff when an array with empty values is sent', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    UpdatePlanRequest::factory()->state([
        'cliff' => [
            'threshold_in_percent' => null,
            'time_scope' => TimeScopeEnum::MONTHLY->value,
        ],
    ])->fake();

    $this->put(route('plans.update', $plan))->assertValid();

    expect(Cliff::count())->toBe(0);
});

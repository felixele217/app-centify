<?php

use App\Models\Plan;

it('can delete a plan as an admin', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    expect(Plan::count())->toBe(1);

    $this->delete(route('plans.destroy', $plan))->assertRedirect();

    expect(Plan::count())->toBe(0);
});

it('cannot delete an agent as a foreign admin', function () {
    signInAdmin();

    $plan = Plan::factory()->create();

    $this->delete(route('plans.destroy', $plan))->assertForbidden();
});

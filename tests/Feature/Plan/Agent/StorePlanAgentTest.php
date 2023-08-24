<?php

use App\Models\Agent;
use App\Models\Plan;

it('store an agent for a plan', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->post(route('agents.plans.store', [Agent::factory()->ofOrganization($admin->organization->id)->create()->id, $plan]))
        ->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe(1);
});

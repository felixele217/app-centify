<?php

use App\Models\Agent;
use App\Models\Plan;

it('can update a plan when only sending assigned agents', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->put(route('plans.agents.update', $plan), [
        'agent_id' => Agent::factory()->ofOrganization($admin->organization->id)->create()->id,
    ])->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe(1);
});

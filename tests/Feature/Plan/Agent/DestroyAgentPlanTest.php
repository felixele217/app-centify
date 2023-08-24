<?php

use App\Models\Agent;
use App\Models\Plan;

it('detach a plan from an agent', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach($agentId = Agent::factory()->ofOrganization($admin->organization->id)->create()->id);

    $this->delete(route('agents.plans.destroy', [$agentId, $plan->id]))
        ->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe(0);
});

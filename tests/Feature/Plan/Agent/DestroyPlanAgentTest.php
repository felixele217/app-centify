<?php

use App\Models\Agent;
use App\Models\Plan;

it('can update a plan when only sending assigned agents', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach($agentId = Agent::factory()->ofOrganization($admin->organization->id)->create()->id);

    $this->delete(route('plans.agents.destroy', $plan), [
        'agent_id' => $agentId,
    ])->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe(0);
});

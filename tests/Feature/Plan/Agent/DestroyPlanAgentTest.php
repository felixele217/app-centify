<?php

use App\Models\Agent;
use App\Models\Plan;

it('detach an agent from a plan', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach($agentId = Agent::factory()->ofOrganization($admin->organization->id)->create()->id);

    $this->delete(route('plans.agents.destroy', [$plan, $agentId]))
        ->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe(0);
});

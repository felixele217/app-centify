<?php

use App\Models\Agent;
use App\Models\Plan;

it('store an agent for a plan', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->post(route('plans.agents.store', [$plan, Agent::factory()->ofOrganization($admin->organization->id)->create()->id]))
        ->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe(1);
});

it('will not store a duplicate if the agent already exists in the plan', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach($agentId = Agent::factory()->ofOrganization($admin->organization->id)->create()->id);

    $this->post(route('plans.agents.store', [$plan, $agentId]))
        ->assertRedirect();

    expect($plan->refresh()->agents->count())->toBe(1);
});
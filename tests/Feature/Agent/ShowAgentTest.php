<?php

use App\Models\Agent;
use App\Models\AgentPlan;
use App\Models\Plan;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    $agent = Agent::factory()->hasPaidLeaves(1)->create([
        'organization_id' => $admin->organization->id,
    ]);

    $plans = Plan::factory($planCount = 2)->create([
        'organization_id' => $admin->organization->id,
    ]);

    foreach ($plans as $plan) {
        AgentPlan::factory()->create([
            'agent_id' => $agent->id,
            'plan_id' => $plan->id,
        ]);
    }

    $this->get(route('agents.show', $agent))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Agent/Show')
                ->where('agent.id', $agent->id)
                ->has('agent.commission')
                ->has('agent.paid_leaves')
                ->has('agent.active_plans', $planCount)
                ->has('agent.paid_leaves_commission')
        );
});

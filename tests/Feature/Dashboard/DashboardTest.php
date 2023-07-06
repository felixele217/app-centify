<?php

use App\Models\Agent;
use App\Models\Plan;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    $plan = Plan::factory()->create([
        'creator_id' => $admin,
        'organization_id' => $admin->organization->id,
    ]);

    $plan->agents()->attach(Agent::factory($agentCount = 10)
        ->hasDeals(3)
        ->create(['organization_id' => $admin->organization->id]));

    $this->get(route('dashboard'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->has('agents', $agentCount)
                ->has('agents.1.deals')
                ->has('agents.1.quota_attainment')
                ->has('agents.1.commission')
        );
});

it('does not send foreign agents', function () {

})->todo();

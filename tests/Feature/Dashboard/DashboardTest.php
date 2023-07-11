<?php

use App\Enum\TimeScopeEnum;
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
                ->where('time_scopes', array_column(TimeScopeEnum::cases(), 'value'))
        );
});

it('does not send foreign agents', function () {
    signInAdmin();

    Agent::factory(5)->create();

    $this->get(route('dashboard'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->has('agents', 0)
        );
});

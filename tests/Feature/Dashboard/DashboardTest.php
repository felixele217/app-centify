<?php

use App\Models\Agent;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    Agent::factory($agentCount = 10)
        ->hasDeals(3)
        ->create(['organization_id' => $admin->organization->id]);

    $this->get(route('dashboard'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('agents', $agentCount)
            ->has('agents.1.deals')
    );
});

it('does not send foreign agents', function () {

})->todo();

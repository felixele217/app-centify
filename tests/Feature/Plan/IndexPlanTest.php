<?php

use App\Models\Plan;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $user = signIn();

    Plan::factory($planCount = 5)->create([
        'organization_id' => $user->organization->id,
    ])->first()->agents()->attach(User::factory($agentCount = 3)->agent()->create());

    Plan::factory(10)->create();

    $this->get(route('plans.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Index')
            ->has('plans', $planCount)
            ->where('plans.0.agents_count', $agentCount)
    );
});

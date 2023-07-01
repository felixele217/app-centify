<?php

use App\Models\Plan;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $user = signIn();

    Plan::factory($planCount = 5)->create([
        'organization_id' => $user->organization->id,
    ]);

    Plan::factory(10)->create();

    $this->get(route('plans.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Index')
            ->has('plans', $planCount)
    );
});

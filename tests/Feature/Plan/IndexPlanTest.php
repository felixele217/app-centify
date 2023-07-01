<?php

use App\Models\Plan;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    signIn();

    $plans = Plan::factory($planCount = 5)->create();

    $this->get(route('plans.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Index')
            ->has('plans', $planCount)
    );
})->skip();

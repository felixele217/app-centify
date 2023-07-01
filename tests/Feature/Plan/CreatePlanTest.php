<?php

use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $user = signIn();
    $this->get(route('plans.create'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Plan/Create')
    );
});

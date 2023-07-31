<?php

use Inertia\Testing\AssertableInertia;

it('always sends user with pipedrive config', function () {
    $admin = signInAdmin();

    $this->get(route('dashboard'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->has('auth.user.organization.active_integrations')
        );
});

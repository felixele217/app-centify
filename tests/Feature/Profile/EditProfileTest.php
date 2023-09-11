<?php

use Inertia\Testing\AssertableInertia;

it('passes correct props', function () {
    $admin = signInAdmin();

    $this->get(route('profile.edit'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Profile/Edit')
                ->where('organization.id', $admin->organization_id)
        );
});

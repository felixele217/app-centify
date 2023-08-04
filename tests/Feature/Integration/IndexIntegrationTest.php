<?php

use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    $this->get(route('integrations'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Integration/Index')
                ->has('activeIntegrations')
        );
});

<?php

use Inertia\Testing\AssertableInertia;

it('shares env variables', function () {
    signInAdmin();

    $this->get(route('dashboard'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('env.ENVIRONMENT', env('APP_ENV'))
        );
});

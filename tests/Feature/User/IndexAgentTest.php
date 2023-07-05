<?php

use App\Models\Agent;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;

it('passes the correct props', function () {
    signInAdmin();

    $agents = Agent::factory(10)->create();

    $this->get(route('agents.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Agent/Index')
            ->has('agents', $agents->count())
    );
});

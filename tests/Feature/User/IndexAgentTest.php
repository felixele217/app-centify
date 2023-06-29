<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;

it('passes the correct props', function () {
    signIn();

    $agents = User::factory(10)->create();

    foreach ($agents as $agent) {
        $agent->assignRole(Role::firstOrCreate([
            'name' => 'agent',
        ]));
    }

    $this->get(route('agents.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Agent/Index')
            ->has('agents', $agents->count())
    );
});

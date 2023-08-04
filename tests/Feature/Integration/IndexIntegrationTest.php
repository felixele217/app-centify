<?php

use App\Models\Integration;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    Integration::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->get(route('integrations.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Integration/Index')
                ->has('integrations.0.custom_fields')
        );
});

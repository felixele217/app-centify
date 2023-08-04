<?php

use App\Enum\CustomFieldEnum;
use App\Models\CustomField;
use App\Models\Integration;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    $customFields = CustomField::factory($customFieldCount = 5)->create([
        'integration_id' => Integration::factory()->create([
            'organization_id' => $admin->organization->id,
        ]),
    ]);

    $this->get(route('integrations.custom-fields.index', $customFields->first()->integration->id))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Integration/CustomField/Index')
                ->has('custom_fields', $customFieldCount)
                ->where('available_custom_field_names', array_column(CustomFieldEnum::cases(), 'value'))
        );
});

it('does not send foreign fields', function () {
    signInAdmin();

    CustomField::factory()->create();

    $foreignIntegration = Integration::factory()->create();

    $this->get(route('integrations.custom-fields.index', $foreignIntegration))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Integration/CustomField/Index')
                ->has('custom_fields', 0)
                ->where('available_custom_field_names', array_column(CustomFieldEnum::cases(), 'value'))
        );
});

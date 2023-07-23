<?php

use App\Enum\CustomIntegrationFieldEnum;
use App\Models\CustomIntegrationField;
use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    $admin = signInAdmin();

    CustomIntegrationField::factory($customIntegrationFieldCount = 5)->create([
        'organization_id' => $admin->organization,
    ]);

    $this->get(route('custom-integration-fields.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('CustomIntegrationField/Index')
                ->has('custom_integration_fields', $customIntegrationFieldCount)
                ->where('available_integration_field_names', array_column(CustomIntegrationFieldEnum::cases(), 'value'))
        );
});

it('does not send foreign fields', function () {
    signInAdmin();

    CustomIntegrationField::factory()->create();

    $this->get(route('custom-integration-fields.index'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('CustomIntegrationField/Index')
                ->has('custom_integration_fields', 0)
                ->where('available_integration_field_names', array_column(CustomIntegrationFieldEnum::cases(), 'value'))
        );
})->todo();

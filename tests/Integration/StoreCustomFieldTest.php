<?php

use App\Enum\CustomIntegrationFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Models\Integration;
use Illuminate\Support\Str;

it('can store an api key for demo set by field for the pipedrive integration as an admin', function () {
    $admin = signInAdmin();

    $integration = Integration::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $this->post(route('integrations.custom-fields.store', $integration), [
        'name' => $name = CustomIntegrationFieldEnum::DEMO_SET_BY->value,
        'api_key' => $apiKey = Str::random(40),
        'integration_type' => IntegrationTypeEnum::PIPEDRIVE->value,
    ])->assertRedirect();

    expect($admin->organization->integrations->first()->customFields->first()->name->value)->toBe($name);
    expect($admin->organization->integrations->first()->customFields->first()->api_key)->toBe($apiKey);
});

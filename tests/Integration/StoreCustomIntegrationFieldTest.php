<?php

use App\Enum\CustomIntegrationFieldEnum;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Support\Str;

it('can store an api key for demo set by field for the pipedrive integration as an admin', function () {
    $admin = signInAdmin();

    $this->post(route('custom-integration-fields.store'), [
        'name' => $name = CustomIntegrationFieldEnum::DEMO_SET_BY->value,
        'api_key' => $apiKey = Str::random(40),
        'integration_type' => IntegrationTypeEnum::PIPEDRIVE->value,
    ])->assertRedirect();

    expect($admin->organization->customIntegrationFields->first()->name->value)->toBe($name);
    expect($admin->organization->customIntegrationFields->first()->api_key)->toBe($apiKey);
});

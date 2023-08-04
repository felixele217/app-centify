<?php

use App\Models\CustomField;
use App\Models\Integration;
use Illuminate\Support\Str;

it('can store an api key for demo set by field for the pipedrive integration as an admin', function () {
    $admin = signInAdmin();

    $customField = CustomField::factory()->create([
        'integration_id' => Integration::factory()->create([
            'organization_id' => $admin->organization->id,
        ]),
    ]);

    $this->put(route('integrations.custom-fields.update', [$customField->integration, $customField]), [
        'api_key' => $newApiKey = Str::random(40),
    ])->assertRedirect();

    expect($admin->organization->integrations->first()->customFields->first()->api_key)->toBe($newApiKey);
});

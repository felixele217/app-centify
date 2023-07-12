<?php

use App\Models\CustomIntegrationField;
use Illuminate\Support\Str;

use function Pest\Laravel\withoutExceptionHandling;

it('can store an api key for demo set by field for the pipedrive integration as an admin', function () {
    $admin = signInAdmin();

    $customIntegrationField = CustomIntegrationField::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);
    withoutExceptionHandling();

    $this->put(route('custom-integration-fields.update', $customIntegrationField), [
        'api_key' => $newApiKey = Str::random(40),
    ])->assertRedirect();

    expect($admin->organization->customIntegrationFields->first()->api_key)->toBe($newApiKey);
});

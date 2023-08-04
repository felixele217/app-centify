<?php

use App\Models\PipedriveConfig;

beforeEach(function () {
    $this->admin = signInAdmin();

    PipedriveConfig::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]);
});

it('redirects to correct route and stores the integration', function () {
    $this->get(route('authenticate.pipedrive.store'))->assertRedirect(route('custom-integration-fields.index'));

    expect($this->admin->organization->integrations)->toHaveCount(1);
    expect($this->admin->organization->integrations->first()->name->value)->toBe('pipedrive');
    expect($this->admin->organization->integrations->first()->subdomain)->not()->toBeNull();
});

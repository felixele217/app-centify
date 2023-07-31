<?php

use App\Models\PipedriveConfig;

beforeEach(function () {
    $this->admin = signInAdmin();

    PipedriveConfig::factory()->create([
        'organization_id' => $this->admin->organization->id,
    ]);
});

it('redirects to correct route', function () {
    $this->get(route('authenticate.pipedrive.store'))->assertRedirect(route('custom-integration-fields.index'));
});

it('sets the subdomain for the organization', function () {
    $this->get(route('authenticate.pipedrive.store'))->assertRedirect();

    expect($this->admin->organization->pipedriveConfig->subdomain)->not()->toBeNull();
});

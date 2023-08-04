<?php

use App\Models\Integration;

beforeEach(function () {
    $this->admin = signInAdmin();
});

it('redirects to correct route and stores the integration', function () {
    $this->get(route('authenticate.pipedrive.store').'?code=12162171.17524624.2e873144dcfb250b66c55c71fef661200da084b4')
        ->assertRedirect(route('integrations.custom-fields.index', Integration::first()));

    expect($this->admin->organization->integrations)->toHaveCount(1);
    expect($this->admin->organization->integrations->first()->name->value)->toBe('pipedrive');
    expect($this->admin->organization->integrations->first()->subdomain)->not()->toBeNull();
    expect($this->admin->organization->integrations->first()->access_token)->not()->toBeNull();
    expect($this->admin->organization->integrations->first()->refresh_token)->not()->toBeNull();
});

<?php

use App\Models\Integration;
use App\Repositories\IntegrationRepository;

it('updating an integration returns it properly', function () {
    $organization = Integration::factory()->create()->organization;

    $integration = IntegrationRepository::update($organization->id, []);

    expect(get_class($integration))->toBe(Integration::class);
});

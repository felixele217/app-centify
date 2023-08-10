<?php

use App\Models\CustomField;
use App\Models\Integration;
use Carbon\Carbon;

it('sets a new last synced at date after syncing', function () {
    $admin = signInAdmin();

    CustomField::factory()->create([
        'integration_id' => $integration = Integration::factory()->create([
            'organization_id' => $admin->organization_id,
            'last_synced_at' => Carbon::yesterday(),
        ]),
        'api_key' => env('PIPEDRIVE_DEMO_SET_BY'),
    ]);

    $previousLastSynced = $integration->last_synced_at;

    $this->get(route('pipedrive.sync').'?redirect_url='.route('integrations.index'))->assertRedirect();

    expect($previousLastSynced->lt($integration->fresh()->last_synced_at))->toBeTrue();
});

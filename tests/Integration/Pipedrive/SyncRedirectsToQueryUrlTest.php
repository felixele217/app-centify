<?php

use App\Models\CustomField;
use App\Models\Integration;

it('redirects to the url in the query after syncing successfully', function () {
    $admin = signInAdmin();

    CustomField::factory()->create([
        'integration_id' => Integration::factory()->create([
            'organization_id' => $admin->organization_id,
        ])->id,
        'api_key' => env('PIPEDRIVE_DEMO_SET_BY'),
    ]);

    $this->get(route('pipedrive.sync').'?redirect_url='.route('integrations.index'))->assertRedirect(route('integrations.index'));
    $this->get(route('pipedrive.sync').'?redirect_url='.route('deals.index'))->assertRedirect(route('deals.index'));
});

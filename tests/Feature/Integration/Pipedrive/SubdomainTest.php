<?php

use App\Actions\SetPipedriveSubdomainAction;
use App\Facades\Pipedrive;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Models\PipedriveConfig;

it('stores the pipedrive subdomain for the organization upon syncing the data', function () {
    $admin = signInAdmin();

    PipedriveConfig::factory()->create([
        'organization_id' => $admin->organization->id,
    ]);

    $deals = json_decode(json_encode(Pipedrive::deals()->all()->getData()), true);

    $subdomain = PipedriveHelper::organizationSubdomain($deals[0]);

    SetPipedriveSubdomainAction::execute($admin->organization);

    expect($admin->organization->pipedriveConfig->subdomain)->toBe($subdomain);
});

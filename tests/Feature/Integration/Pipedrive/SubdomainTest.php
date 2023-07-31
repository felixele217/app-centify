<?php

use App\Actions\PipedriveSubdomainAction;

it('stores the pipedrive subdomain for the organization upon syncing the data', function () {
    $admin = signInAdmin();

    $subdomain = PipedriveSubdomainAction::execute();

    expect($admin->pipedriveConfig->subdomain)->toBe($subdomain);
})->todo();

<?php

declare(strict_types=1);

namespace App\Actions;

use App\Facades\Pipedrive;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Models\Organization;

class SetPipedriveSubdomainAction
{
    public static function execute(Organization $organization): void
    {
        $deals = json_decode(json_encode(Pipedrive::deals()->all()->getData()), true);

        $organization->pipedriveConfig->update([
            'subdomain' => PipedriveHelper::organizationSubdomain($deals[0]),
        ]);
    }
}

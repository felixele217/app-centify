<?php

declare(strict_types=1);

namespace App\Actions;

use App\Facades\Pipedrive;
use App\Integrations\Pipedrive\PipedriveHelper;

class PipedriveSubdomainAction
{
    public static function execute (): string
    {
        $deals = json_decode(json_encode(Pipedrive::deals()->all()->getData()), true);



        return PipedriveHelper::organizationSubdomain($deals[0]);
    }
}

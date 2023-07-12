<?php

namespace App\Integrations\Pipedrive;

class PipedriveHelper
{
    public static function demoSetByEmail(array $deal): string|null
    {
        return $deal[env('PIPEDRIVE_DEMO_SET_BY')]
        ? $deal[env('PIPEDRIVE_DEMO_SET_BY')]['email'][0]['value']
        : null;
    }
}

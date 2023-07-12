<?php

namespace App\Integrations\Pipedrive;

class PipedriveHelper
{
    public static function demoSetByEmail(array $deal, string $demoSetByApiKey = null): string|null
    {
        $demoSetByApiKey = $demoSetByApiKey ?? env('PIPEDRIVE_DEMO_SET_BY');

        return $deal[$demoSetByApiKey]
        ? $deal[$demoSetByApiKey]['email'][0]['value']
        : null;
    }
}

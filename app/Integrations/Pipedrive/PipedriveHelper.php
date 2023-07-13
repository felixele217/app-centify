<?php

namespace App\Integrations\Pipedrive;

use App\Exceptions\InvalidApiKeyException;

class PipedriveHelper
{
    public static function demoSetByEmail(array $deal, string $demoSetByApiKey = null): string|null
    {
        $demoSetByApiKey = $demoSetByApiKey ?? env('PIPEDRIVE_DEMO_SET_BY');

        if (! array_key_exists($demoSetByApiKey, $deal)) {
            throw new InvalidApiKeyException('Invalid api key!');
        }

        return $deal[$demoSetByApiKey]
        ? $deal[$demoSetByApiKey]['email'][0]['value']
        : null;
    }
}

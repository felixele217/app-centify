<?php

namespace App\Integrations\Pipedrive;

use App\Exceptions\InvalidApiKeyException;

class PipedriveHelper
{
    public static function demoSetByEmail(array $deal, string $demoSetByApiKey = null): string|null
    {
        $demoSetByApiKey = $demoSetByApiKey ?? env('PIPEDRIVE_DEMO_SET_BY');

        if (! array_key_exists($demoSetByApiKey, $deal)) {

            report(new InvalidApiKeyException($errorMessage = 'Invalid demo_set_by api key!'));

            return back()->withErrors([
                'invalid_api_key' => $errorMessage,
            ]);
        }

        return $deal[$demoSetByApiKey]
        ? $deal[$demoSetByApiKey]['email'][0]['value']
        : null;
    }
}

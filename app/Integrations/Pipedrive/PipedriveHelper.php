<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Exceptions\InvalidApiKeyException;
use Illuminate\Http\RedirectResponse;

class PipedriveHelper
{
    public static function demoSetByEmail(array $deal, string $demoSetByApiKey = null): string|RedirectResponse|null
    {
        $demoSetByApiKey = $demoSetByApiKey ?? env('PIPEDRIVE_DEMO_SET_BY');

        if (! array_key_exists($demoSetByApiKey, $deal)) {

            report(new InvalidApiKeyException($errorMessage = "Invalid demo_set_by api key! Please check your integration's settings."));

            return back()->withErrors([
                'invalid_api_key' => $errorMessage,
            ]);
        }

        return $deal[$demoSetByApiKey]
        ? $deal[$demoSetByApiKey]['email'][0]['value']
        : null;
    }

    public static function organizationSubdomain(array $deal): string
    {
        return strtok($deal['org_id']['cc_email'], '@');
    }
}

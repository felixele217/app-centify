<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Exceptions\InvalidApiKeyException;
use Illuminate\Http\RedirectResponse;

class PipedriveHelper
{
    public static function demoSetByEmail(array $deal, ?string $demoSetByApiKey): string|RedirectResponse|null
    {
        if (! $demoSetByApiKey || ! array_key_exists($demoSetByApiKey, $deal)) {
            throw new InvalidApiKeyException();
        }

        if (! isset($deal[$demoSetByApiKey])) {
            return null;
        }

        return gettype($deal[$demoSetByApiKey]['email']) === 'string'
        ? $deal[$demoSetByApiKey]['email']
        : $deal[$demoSetByApiKey]['email'][0]['value'];
    }

    public static function organizationSubdomain(array $deal): string
    {
        return strtok($deal['org_id']['cc_email'], '@');
    }
}

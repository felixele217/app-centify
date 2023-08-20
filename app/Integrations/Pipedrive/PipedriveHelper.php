<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Models\Agent;
use App\Enum\DealStatusEnum;
use Illuminate\Http\RedirectResponse;
use App\Exceptions\InvalidApiKeyException;

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

    public static function ownerEmail(array $deal): string
    {
        return gettype($deal['person_id']['email']) === 'string'
            ? $deal['person_id']['email']
            : $deal['person_id']['email'][0]['value'];
    }

    public static function organizationSubdomain(array $deal): string
    {
        return strtok($deal['org_id']['cc_email'], '@');
    }

    public static function wonDeal(string $email, array $integrationDealArray ): bool
    {
        return PipedriveHelper::ownerEmail($integrationDealArray) === $email && $integrationDealArray['status'] === DealStatusEnum::WON->value;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Integration;

class IntegrationRepository
{
    public static function create(int $organizationId, array $integrationFields): Integration
    {
        return Integration::create([
            'organization_id' => $organizationId,
            'name' => $integrationFields['name'],
            'subdomain' => $integrationFields['subdomain'],
        ]);
    }
}

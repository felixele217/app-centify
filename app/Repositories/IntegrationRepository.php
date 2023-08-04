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
            ...$integrationFields,
        ]);
    }

    public static function update(int $organizationId, array $integrationFields): void
    {
        Integration::whereOrganizationId($organizationId)->update([
            ...$integrationFields,
        ]);
    }

    public static function updateOrCreate(int $organizationId, array $integrationFields): void
    {
        Integration::updateOrCreate(
            ['organization_id' => $organizationId],
            $integrationFields
        );
    }
}

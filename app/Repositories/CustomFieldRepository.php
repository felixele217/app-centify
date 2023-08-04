<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\StoreCustomFieldRequest;
use App\Http\Requests\UpdateCustomIntegrationFieldRequest;
use App\Models\CustomField;
use App\Models\CustomIntegrationField;

class CustomFieldRepository
{
    public static function create(StoreCustomFieldRequest $request, int $integrationId): CustomField
    {
        return CustomField::create([
            ...$request->validated(),
            'integration_id' => $integrationId,
        ]);
    }

    public static function update(CustomIntegrationField $customIntegrationField, UpdateCustomIntegrationFieldRequest $request): void
    {
        $customIntegrationField->update($request->validated());
    }
}

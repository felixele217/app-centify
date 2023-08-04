<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\StoreCustomFieldRequest;
use App\Http\Requests\UpdateCustomFieldRequest;
use App\Models\CustomField;

class CustomFieldRepository
{
    public static function create(StoreCustomFieldRequest $request, int $integrationId): CustomField
    {
        return CustomField::create([
            ...$request->validated(),
            'integration_id' => $integrationId,
        ]);
    }

    public static function update(UpdateCustomFieldRequest $request, CustomField $customField): void
    {
        $customField->update($request->validated());
    }
}

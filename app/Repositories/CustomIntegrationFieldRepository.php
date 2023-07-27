<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\StoreCustomIntegrationFieldRequest;
use App\Http\Requests\UpdateCustomIntegrationFieldRequest;
use App\Models\CustomIntegrationField;
use Illuminate\Support\Facades\Auth;

class CustomIntegrationFieldRepository
{
    public static function create(StoreCustomIntegrationFieldRequest $request): CustomIntegrationField
    {
        return CustomIntegrationField::create([
            ...$request->validated(),
            'organization_id' => Auth::user()->organization->id,
        ]);
    }

    public static function update(CustomIntegrationField $customIntegrationField, UpdateCustomIntegrationFieldRequest $request): void
    {
        $customIntegrationField->update($request->validated());
    }
}

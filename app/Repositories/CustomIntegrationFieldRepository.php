<?php

namespace App\Repositories;

use App\Http\Requests\StoreCustomIntegrationFieldRequest;
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
}

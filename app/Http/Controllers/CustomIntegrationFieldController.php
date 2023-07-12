<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreCustomIntegrationFieldRequest;
use App\Models\CustomIntegrationField;
use Illuminate\Support\Facades\Auth;

class CustomIntegrationFieldController extends Controller
{
    public function store(StoreCustomIntegrationFieldRequest $request): RedirectResponse
    {
        CustomIntegrationField::create([
            ...$request->validated(),
            'organization_id' => Auth::user()->organization->id
        ]);

        return back();
    }
}

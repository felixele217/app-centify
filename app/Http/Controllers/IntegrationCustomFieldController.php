<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Integration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\CustomIntegrationField;
use App\Enum\CustomIntegrationFieldEnum;
use App\Repositories\CustomFieldRepository;
use App\Http\Requests\StoreCustomFieldRequest;
use App\Repositories\CustomIntegrationFieldRepository;
use App\Http\Requests\UpdateCustomIntegrationFieldRequest;

class IntegrationCustomFieldController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('CustomIntegrationField/Index', [
            'custom_integration_fields' => Auth::user()->organization->customIntegrationFields,
            'available_integration_field_names' => array_column(CustomIntegrationFieldEnum::cases(), 'value'),
        ]);
    }

    public function store(StoreCustomFieldRequest $request, Integration $integration): RedirectResponse
    {
        CustomFieldRepository::create($request, $integration->id);

        return back();
    }

    public function update(UpdateCustomIntegrationFieldRequest $request, CustomIntegrationField $customIntegrationField): RedirectResponse
    {
        CustomIntegrationFieldRepository::update($customIntegrationField, $request);

        return back();
    }
}

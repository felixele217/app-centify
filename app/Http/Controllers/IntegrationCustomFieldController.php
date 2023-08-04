<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\CustomFieldEnum;
use App\Http\Requests\StoreCustomFieldRequest;
use App\Http\Requests\UpdateCustomIntegrationFieldRequest;
use App\Models\CustomIntegrationField;
use App\Models\Integration;
use App\Repositories\CustomFieldRepository;
use App\Repositories\CustomIntegrationFieldRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IntegrationCustomFieldController extends Controller
{
    public function index(Integration $integration): Response
    {
        return Inertia::render('Integration/CustomField/Index', [
            'custom_fields' => $integration->customFields,
            'available_custom_field_names' => array_column(CustomFieldEnum::cases(), 'value'),
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

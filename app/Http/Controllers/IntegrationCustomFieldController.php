<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\CustomFieldEnum;
use App\Http\Requests\StoreCustomFieldRequest;
use App\Http\Requests\UpdateCustomFieldRequest;
use App\Models\CustomField;
use App\Models\Integration;
use App\Repositories\CustomFieldRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IntegrationCustomFieldController extends Controller
{
    public function index(Integration $integration): Response
    {
        return Inertia::render('Integration/CustomField/Index', [
            'integration' => $integration->load('customFields'),
            'available_custom_field_names' => array_column(CustomFieldEnum::cases(), 'value'),
        ]);
    }

    public function store(StoreCustomFieldRequest $request, Integration $integration): RedirectResponse
    {
        CustomFieldRepository::create($request, $integration->id);

        return back();
    }

    public function update(UpdateCustomFieldRequest $request, Integration $integration, CustomField $customField): RedirectResponse
    {
        CustomFieldRepository::update($request, $customField);

        return back();
    }
}

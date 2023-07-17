<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\CustomIntegrationFieldEnum;
use App\Http\Requests\StoreCustomIntegrationFieldRequest;
use App\Http\Requests\UpdateCustomIntegrationFieldRequest;
use App\Models\CustomIntegrationField;
use App\Repositories\CustomIntegrationFieldRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CustomIntegrationFieldController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('CustomIntegrationField/Index', [
            'custom_integration_fields' => Auth::user()->organization->customIntegrationFields,
            'available_integration_field_names' => array_column(CustomIntegrationFieldEnum::cases(), 'value'),
        ]);
    }

    public function store(StoreCustomIntegrationFieldRequest $request): RedirectResponse
    {
        CustomIntegrationFieldRepository::create($request);

        return back();
    }

    public function update(UpdateCustomIntegrationFieldRequest $request, CustomIntegrationField $customIntegrationField): RedirectResponse
    {
        CustomIntegrationFieldRepository::update($customIntegrationField, $request);

        return back();
    }
}

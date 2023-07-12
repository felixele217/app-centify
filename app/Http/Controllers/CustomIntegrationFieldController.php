<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\CustomIntegrationField;
use App\Enum\CustomIntegrationFieldEnum;
use App\Repositories\CustomIntegrationFieldRepository;
use App\Http\Requests\StoreCustomIntegrationFieldRequest;
use App\Http\Requests\UpdateCustomIntegrationFieldRequest;

class CustomIntegrationFieldController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('CustomIntegrationField/Index', [
            'custom_integration_fields' => Auth::user()->organization->customIntegrationFields,
            'available_integration_fields' => array_column(CustomIntegrationFieldEnum::cases(), 'value'),
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

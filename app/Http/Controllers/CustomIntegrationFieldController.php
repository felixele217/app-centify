<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomIntegrationFieldRequest;
use App\Repositories\CustomIntegrationFieldRepository;
use Illuminate\Http\RedirectResponse;

class CustomIntegrationFieldController extends Controller
{
    public function store(StoreCustomIntegrationFieldRequest $request): RedirectResponse
    {
        CustomIntegrationFieldRepository::create($request);

        return back();
    }
}

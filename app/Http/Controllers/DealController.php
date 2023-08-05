<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Deal;
use Inertia\Inertia;
use Inertia\Response;
use App\Enum\DealScopeEnum;
use App\Repositories\DealRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateDealRequest;

class DealController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Deal/Index', [
            'deals' => DealRepository::get(DealScopeEnum::tryFrom(request()->query('scope') ?? '')),
            'integrations' => Auth::user()->organization->integrations,
        ]);
    }

    public function update(UpdateDealRequest $request, Deal $deal): RedirectResponse
    {
        DealRepository::update($deal, $request);

        return back();
    }
}

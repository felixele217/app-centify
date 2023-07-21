<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\DealScopeEnum;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Deal;
use App\Repositories\DealRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DealController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Deal/Index', [
            'deals' => DealRepository::get(DealScopeEnum::tryFrom(request()->query('scope') ?? '')),
        ]);
    }

    public function update(UpdateDealRequest $request, Deal $deal): RedirectResponse
    {
        DealRepository::update($deal, $request);

        return back();
    }
}

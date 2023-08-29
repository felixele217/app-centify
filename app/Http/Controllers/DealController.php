<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\DealScopeEnum;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Deal;
use App\Repositories\DealRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DealController extends Controller
{
    public function index(): Response
    {
        $paginatedDealsObject = DealRepository::dealsForOrganization(Auth::user()->organization, DealScopeEnum::tryFrom(request()->query('scope') ?? ''))
            ->with('agents')
            ->paginate(20);

        $paginatedDealsObject->getCollection()->each->append([
            'active_rejection',
            's_d_r',
            'a_e',
            'demo_scheduled_shareholders',
            'deal_won_shareholders',
        ]);

        return Inertia::render('Deal/Index', [
            'paginatedDealsObject' => $paginatedDealsObject,
            'integrations' => Auth::user()->organization->integrations->load('customFields'),
            'agents' => Auth::user()->organization->agents->pluck('id', 'name'),
        ]);
    }

    public function update(UpdateDealRequest $request, Deal $deal): RedirectResponse
    {
        DealRepository::update($deal, $request);

        return back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Enum\PayoutFrequencyEnum;
use App\Enum\TargetVariableEnum;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Plan/Index', [
            'plans' => Plan::withCount('agents')
                ->with('creator')
                ->whereOrganizationId(Auth::user()->organization->id)->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Plan/Create', [
            'agents' => Auth::user()->organization->agents()->select('id', 'name')->get(),
            'payout_frequency_options' => array_column(PayoutFrequencyEnum::cases(), 'value'),
            'target_variable_options' => array_column(TargetVariableEnum::cases(), 'value'),
        ]);
    }

    public function store(StorePlanRequest $request): RedirectResponse
    {
        PlanRepository::create($request);

        return to_route('plans.index');
    }

    public function edit(Plan $plan): Response
    {
        return Inertia::render('Plan/Edit', [
            'agents' => Auth::user()->organization->agents()->select('id', 'name')->get(),
            'payout_frequency_options' => array_column(PayoutFrequencyEnum::cases(), 'value'),
            'target_variable_options' => array_column(TargetVariableEnum::cases(), 'value'),
            'plan' => $plan->load([
                'agents',
                'cliff',
            ]),
        ]);
    }

    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $this->authorize('any', $plan);

        PlanRepository::update($plan, $request);

        return to_route('plans.index');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $this->authorize('any', $plan);

        $plan->delete();

        return back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;
use App\Enum\TargetVariableEnum;
use App\Enum\PayoutFrequencyEnum;
use App\Repositories\PlanRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;

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

    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $this->authorize('any', $plan);

        PlanRepository::update($plan, $request);

        return back();
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $this->authorize('any', $plan);

        $plan->delete();

        return back();
    }
}

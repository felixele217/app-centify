<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\KickerTypeEnum;
use App\Enum\PlanCycleEnum;
use App\Enum\SalaryTypeEnum;
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
                ->with([
                    'creator',
                    'kicker',
                    'cliff',
                    'cap',
                ])
                ->whereOrganizationId(Auth::user()->organization->id)->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Plan/Create', [
            'agents' => Auth::user()->organization->agents,
            'payout_frequency_options' => array_column(PlanCycleEnum::cases(), 'value'),
            'target_variable_options' => array_column(TargetVariableEnum::cases(), 'value'),
            'kicker_type_options' => array_column(KickerTypeEnum::cases(), 'value'),
            'salary_type_options' => array_column(SalaryTypeEnum::cases(), 'value'),
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
            'agents' => Auth::user()->organization->agents,
            'payout_frequency_options' => array_column(PlanCycleEnum::cases(), 'value'),
            'target_variable_options' => array_column(TargetVariableEnum::cases(), 'value'),
            'kicker_type_options' => array_column(KickerTypeEnum::cases(), 'value'),
            'salary_type_options' => array_column(SalaryTypeEnum::cases(), 'value'),
            'plan' => $plan->load([
                'agents',
                'cliff',
                'kicker',
                'cap',
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

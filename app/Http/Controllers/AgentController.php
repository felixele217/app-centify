<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\AgentStatusEnum;
use App\Enum\ContinuationOfPayTimeScopeEnum;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\Plan;
use App\Repositories\AgentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Agent/Index', [
            'agents' => Agent::with('activePaidLeave')->whereOrganizationId(Auth::user()->organization->id)->get()->append('active_plans'),
            'possible_statuses' => array_column(AgentStatusEnum::cases(), 'value'),
            'continuation_of_pay_time_scope_options' => array_column(ContinuationOfPayTimeScopeEnum::cases(), 'value'),
            'plans' => Plan::whereOrganizationId(Auth::user()->organization->id)->select('id', 'name')->get(),
        ]);
    }

    public function store(StoreAgentRequest $request): RedirectResponse
    {
        AgentRepository::create($request);

        return back();
    }

    public function show(Agent $agent): Response
    {
        return Inertia::render('Agent/Show', [
            'agent' => $agent->load('paidLeaves')->append([
                'quota_attainment_in_percent',
                'active_plans',
                'commission',
                'paid_leaves_commission',
                'sick_leaves_days_count',
                'vacation_leaves_days_count',
            ]),
        ]);
    }

    public function update(UpdateAgentRequest $request, Agent $agent): RedirectResponse
    {
        $this->authorize('any', $agent);

        AgentRepository::update($agent, $request);

        return back();
    }

    public function destroy(Agent $agent): RedirectResponse
    {
        $this->authorize('any', $agent);

        $agent->delete();

        return back();
    }
}

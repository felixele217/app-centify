<?php

namespace App\Http\Controllers;

use App\Enum\AgentStatusEnum;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Repositories\AgentRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Agent/Index', [
            'agents' => Agent::all(),
            'possible_statuses' => array_column(AgentStatusEnum::cases(), 'value'),
        ]);
    }

    public function store(StoreAgentRequest $request): RedirectResponse
    {
        AgentRepository::create($request);

        return back();
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

<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Agent;
use Inertia\Response;
use App\Repositories\AgentRepository;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;

class AgentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Agent/Index', [
            'agents' => Agent::all(),
        ]);
    }

    public function store(StoreAgentRequest $request): RedirectResponse
    {
        AgentRepository::create($request);

        return back();
    }

    public function update(UpdateAgentRequest $request, Agent $agent): RedirectResponse
    {
        $this->authorize($agent);

        AgentRepository::update($agent, $request);

        return back();
    }

    public function destroy(Agent $agent): RedirectResponse
    {
        $this->authorize($agent);

        $agent->delete();

        return back();
    }
}

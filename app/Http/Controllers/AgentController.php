<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgentRequest;
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
        ]);
    }

    public function store(StoreAgentRequest $request): RedirectResponse
    {
        AgentRepository::create($request);

        return back();
    }

    public function destroy(Agent $agent): RedirectResponse
    {
        $this->authorize($agent);

        $agent->delete();

        return back();
    }
}

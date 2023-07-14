<?php

namespace App\Repositories;

use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use Illuminate\Support\Facades\Auth;

class AgentRepository
{
    public static function create(StoreAgentRequest $request): Agent
    {
        return Agent::create([
            ...$request->validated(),
            'organization_id' => Auth::user()->organization->id,
        ]);
    }

    public static function update(Agent $agent, UpdateAgentRequest $request): void
    {
        $agent->update([
            ...$request->validated(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use Illuminate\Support\Facades\Auth;

class AgentRepository
{
    public static function create(StoreAgentRequest $request): Agent
    {
        $agent = Agent::create([
            ...$request->validated(),
            'organization_id' => Auth::user()->organization->id,
        ]);

        return $agent;
    }

    public static function update(Agent $agent, UpdateAgentRequest $request): void
    {
        $agent->update([
            ...$request->validated(),
        ]);
    }
}

<?php

namespace App\Repositories;

use App\Http\Requests\StoreAgentRequest;
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
}

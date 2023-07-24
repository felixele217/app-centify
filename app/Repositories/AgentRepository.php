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
        $agent = Agent::create([
            ...$request->safe()->except([
                'paid_leave',
                'status',
            ]),
            'organization_id' => Auth::user()->organization->id,
        ]);

        if ($request->validated('paid_leave')) {
            PaidLeaveRepository::create($agent, $request);
        }

        return $agent;
    }

    public static function update(Agent $agent, UpdateAgentRequest $request): void
    {
        $agent->update([
            ...$request->safe()->except([
                'paid_leave',
                'status',
            ]
            ),
        ]);

        if ($request->validated('paid_leave')) {
            PaidLeaveRepository::update($agent, $request);
        }
    }
}

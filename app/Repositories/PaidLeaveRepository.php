<?php

namespace App\Repositories;

use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\PaidLeave;

class PaidLeaveRepository
{
    public static function create(Agent $agent, StoreAgentRequest|UpdateAgentRequest $request): PaidLeave
    {
        return PaidLeave::create([
            'agent_id' => $agent->id,
            ...$request->validated('paid_leave'),
            'reason' => $request->validated('status'),
        ]);
    }
}
<?php

namespace App\Repositories;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class PlanRepository
{
    public static function create(StorePlanRequest $request): Plan
    {
        $plan = Plan::create([
            ...$request->safe()->except('assigned_agent_ids'),
            'creator_id' => Auth::user()->id,
            'organization_id' => Auth::user()->organization->id,
        ]);

        $plan->agents()->attach($request->validated('assigned_agent_ids'));

        return $plan;
    }

    public static function update(Plan $plan, UpdatePlanRequest $request): Plan
    {
        $plan->update($request->safe()->except('assigned_agent_ids'));

        // dd($plan->agents->pluck('id'), $request->validated('assigned_agent_ids'));
        foreach ($plan->agents as $agent) {
            if (! in_array($agent->id, $request->validated('assigned_agent_ids'))) {
                $plan->agents()->detach($agent->id);
            }
        }

        // dd($request->validated('assigned_agent_ids')[0], $plan->agents->first()->id);
        foreach ($request->validated('assigned_agent_ids') as $agentId) {
            if (! $plan->agents->contains($agentId)) {
                $plan->agents()->attach($agentId);
            }
        }

        return $plan;
    }
}

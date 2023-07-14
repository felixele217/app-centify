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

        // $plan->agents()->attach($request->validated('assigned_agent_ids'));

        return $plan;
    }
}

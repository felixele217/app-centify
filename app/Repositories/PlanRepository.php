<?php

namespace App\Repositories;

use App\Http\Requests\StorePlanRequest;
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

        $plan->users()->attach($request->validated('assigned_agent_ids'));

        return $plan;
    }
}

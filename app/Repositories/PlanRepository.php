<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\PlanData;
use App\Http\Requests\StorePlanRequest;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class PlanRepository
{
    const PLAN_FIELDS = [
        'name',
        'start_date',
        'target_amount_per_month',
        'target_variable',
        'payout_frequency',
    ];

    public static function create(StorePlanRequest $request): Plan
    {
        $plan = Plan::create([
            ...$request->safe()->only(self::PLAN_FIELDS),
            'creator_id' => Auth::user()->id,
            'organization_id' => Auth::user()->organization->id,
        ]);

        if (isset($request->validated('cliff')['threshold_in_percent'])) {
            $plan->cliff()->create($request->validated('cliff'));
        }

        if (isset($request->validated('kicker')['type'])) {
            $plan->kicker()->create($request->validated('kicker'));
        }

        if ($request->validated('cap')) {
            $plan->cap()->create([
                'value' => $request->validated('cap'),
            ]);
        }

        $plan->agents()->attach($request->validated('assigned_agent_ids'));

        return $plan;
    }

    public static function update(Plan $plan, PlanData $planData): Plan
    {
        $fieldsToUpdate = array_intersect_key($planData->toArray(), array_flip(self::PLAN_FIELDS));

        $plan->update($fieldsToUpdate);

        foreach ($plan->agents as $agent) {
            if (! in_array($agent->id, $planData->assigned_agent_ids)) {
                $plan->agents()->detach($agent->id);
            }
        }

        foreach ($planData->assigned_agent_ids as $agentId) {
            if (! $plan->agents->contains($agentId)) {
                $plan->agents()->attach($agentId);
            }
        }

        if (isset($planData->cliff->threshold_in_percent)) {
            $plan->cliff()->updateOrCreate(
                ['plan_id' => $plan->id],
                $planData->cliff->toArray(),
            );
        }

        if (isset($planData->kicker->threshold_in_percent)) {
            $plan->kicker()->updateOrCreate(
                ['plan_id' => $plan->id],
                $planData->kicker->toArray(),
            );
        }

        if ($planData->cap) {
            $plan->cap()->updateOrCreate(
                ['plan_id' => $plan->id],
                ['value' => $planData->cap],
            );
        }

        return $plan;
    }
}

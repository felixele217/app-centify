<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\AgentPlan;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class PlanRepository
{
    const PLAN_FIELDS = [
        'name',
        'start_date',
        'target_amount_per_month',
        'target_variable',
        'plan_cycle',
        'trigger',
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

        if ($request->validated('assigned_agents')) {
            foreach ($request->validated('assigned_agents') as $assignedAgent) {
                AgentPlan::create([
                    'plan_id' => $plan->id,
                    'agent_id' => $assignedAgent['id'],
                    'share_of_variable_pay' => $assignedAgent['share_of_variable_pay'],
                ]);
            }
        }

        return $plan;
    }

    public static function update(Plan $plan, UpdatePlanRequest $request): Plan
    {
        $plan->update($request->safe()->only(self::PLAN_FIELDS));

        foreach ($plan->agents as $agent) {
            if (! $request->validated('assigned_agents') || ! in_array($agent->id, array_column($request->validated('assigned_agents'), 'id'))) {
                $plan->agents()->whereAgentId($agent->id)->delete();
            }
        }

        if ($request->validated('assigned_agents')) {
            foreach ($request->validated('assigned_agents') as $assignedAgent) {
                AgentPlan::updateOrCreate([
                    'plan_id' => $plan->id,
                    'agent_id' => $assignedAgent['id'],
                ], [
                    'share_of_variable_pay' => $assignedAgent['share_of_variable_pay'],
                ]);
            }
        }

        if (isset($request->validated('cliff')['threshold_in_percent'])) {
            $plan->cliff()->updateOrCreate(
                ['plan_id' => $plan->id],
                $request->validated('cliff'),
            );
        }

        if (isset($request->validated('kicker')['type'])) {
            $plan->kicker()->updateOrCreate(
                ['plan_id' => $plan->id],
                $request->validated('kicker'),
            );
        }

        if ($request->validated('cap')) {
            $plan->cap()->updateOrCreate(
                ['plan_id' => $plan->id],
                ['value' => $request->validated('cap')],
            );
        }

        return $plan;
    }

    public static function storeAgent(Plan $plan, int $agentId): void
    {
        $plan->agents()->attach($agentId);
    }

    public static function destroyAgent(Plan $plan, int $agentId): void
    {
        $plan->agents()->whereAgentId($agentId)->detach();
    }
}

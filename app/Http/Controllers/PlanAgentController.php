<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DestroyPlanAgentRequest;
use App\Http\Requests\StorePlanAgentRequest;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use Illuminate\Http\RedirectResponse;

class PlanAgentController extends Controller
{
    public function store(StorePlanAgentRequest $request, Plan $plan): RedirectResponse
    {
        PlanRepository::storeAgent($plan, $request->validated('agent_id'));

        return back();
    }

    public function destroy(DestroyPlanAgentRequest $request, Plan $plan): RedirectResponse
    {
        PlanRepository::destroyAgent($plan, $request->validated('agent_id'));

        return back();
    }
}

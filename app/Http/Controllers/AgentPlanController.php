<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use Illuminate\Http\RedirectResponse;

class AgentPlanController extends Controller
{
    public function store(Agent $agent, Plan $plan): RedirectResponse
    {
        PlanRepository::storeAgent($plan, $agent->id);

        return back();
    }

    public function destroy(Agent $agent, Plan $plan): RedirectResponse
    {
        PlanRepository::destroyAgent($plan, $agent->id);

        return back();
    }
}

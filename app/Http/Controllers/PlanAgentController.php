<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use Illuminate\Http\RedirectResponse;

class PlanAgentController extends Controller
{
    public function store(Plan $plan, Agent $agent): RedirectResponse
    {
        PlanRepository::storeAgent($plan, $agent->id);

        return back();
    }

    public function destroy(Plan $plan, Agent $agent): RedirectResponse
    {
        PlanRepository::destroyAgent($plan, $agent->id);

        return back();
    }
}

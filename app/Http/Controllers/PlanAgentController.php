<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanAgentRequest;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use Illuminate\Http\RedirectResponse;

class PlanAgentController extends Controller
{
    public function store(StorePlanAgentRequest $request, Plan $plan): RedirectResponse
    {
        PlanRepository::addAgent($plan, $request->validated('agent_id'));

        return back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use App\Enum\TargetVariableEnum;
use App\Enum\PayoutFrequencyEnum;
use App\Repositories\PlanRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePlanRequest;

class PlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Plan/Index', [
            'plans' => Plan::withCount('agents')->whereOrganizationId(Auth::user()->organization->id)->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Plan/Create', [
            'agents' => Auth::user()->organization->agents()->select('id', 'name')->get(),
            'payout_frequency_options' => array_column(PayoutFrequencyEnum::cases(), 'value'),
            'target_variable_options' => array_column(TargetVariableEnum::cases(), 'value'),
        ]);
    }

    public function store(StorePlanRequest $request)
    {
        PlanRepository::create($request);

        return to_route('plans.index');
    }
}

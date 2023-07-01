<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Repositories\PlanRepository;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Plan/Index', [
            'plans' => Auth::user()->organization->plans,
        ]);
    }

    public function create()
    {
        return Inertia::render('Plan/Create');
    }

    public function store(StorePlanRequest $request)
    {
        PlanRepository::create($request);

        return to_route('plans.index');
    }
}

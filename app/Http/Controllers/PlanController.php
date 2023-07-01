<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Repositories\PlanRepository;

class PlanController extends Controller
{
    public function index()
    {

    }

    public function store(StorePlanRequest $request)
    {
        PlanRepository::create($request);

        return to_route('plans.index');
    }
}

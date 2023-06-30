<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgentRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Agent/Index', [
            'agents' => User::role('agent')->get(),
        ]);
    }

    public function store(StoreAgentRequest $request): RedirectResponse
    {
        User::create($request->validated())->assignRole('agent');

        return back();
    }
}

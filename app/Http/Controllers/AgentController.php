<?php

namespace App\Http\Controllers;

use App\Enum\RoleEnum;
use App\Http\Requests\StoreAgentRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Agent/Index', [
            'agents' => User::role(RoleEnum::AGENT->value)->get(),
        ]);
    }

    public function store(StoreAgentRequest $request): RedirectResponse
    {
        UserRepository::create($request, RoleEnum::AGENT);

        return back();
    }
}

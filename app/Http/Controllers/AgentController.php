<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreUserRequest;
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

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated())->assignRole('agent');

        return back();
    }
}

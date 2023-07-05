<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Todo/Index', [
            'deals' => Deal::whereHas('agent.organization', function ($query) {
                $query->where('id', Auth::user()->organization->id);
            })->with('agent')->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Todo/Index');
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IntegrationController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Integration/Index', [
            'activeIntegrations' => Auth::user()->organization->activeIntegrations,
        ]);
    }
}

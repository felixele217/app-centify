<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Integrations', [
            'integrations' => Auth::user()->organization->activeIntegrations,
        ]);
    }
}

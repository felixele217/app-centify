<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class SalesforceAuthController extends Controller
{
    public function create(): RedirectResponse
    {
        return Forrest::authenticate();
    }

    public function store(): RedirectResponse
    {
        Forrest::callback();

        return to_route('dashboard');
    }
}

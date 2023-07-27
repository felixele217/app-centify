<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class SalesforceAuthController extends Controller
{
    public function create()
    {
        return Forrest::authenticate();
    }

    public function store()
    {
        Forrest::callback();

        return to_route('dashboard');
    }
}

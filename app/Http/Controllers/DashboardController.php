<?php

namespace App\Http\Controllers;

use App\Enum\TimeScopeEnum;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'agents' => Auth::user()->organization->agents->load('deals')->append([
                'quota_attainment',
                'commission',
            ]),
            'time_scopes' => array_column(TimeScopeEnum::cases(), 'value'),
        ]);
    }
}

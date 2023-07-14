<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Inertia\Inertia;
use App\Enum\TimeScopeEnum;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;

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
            'todo_count' => Deal::whereNull('accepted_at')->whereHas('agent', function (Builder $query) {
                $query->whereOrganizationId(Auth::user()->organization->id);
            })->count(),
        ]);
    }
}

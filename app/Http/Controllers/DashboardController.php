<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\TimeScopeEnum;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'agents' => Auth::user()->organization->agents->load('deals')->append([
                'quota_attainment',
                'quota_attainment_change',
                'commission',
                'commission_change',
                'sick_leaves_days_count',
                'vacation_leaves_days_count',
            ]),
            'time_scopes' => array_column(TimeScopeEnum::cases(), 'value'),
            'open_deal_count' => Deal::whereNull('accepted_at')->whereHas('agent', function (Builder $query) {
                $query->whereOrganizationId(Auth::user()->organization->id);
            })->count(),
        ]);
    }
}

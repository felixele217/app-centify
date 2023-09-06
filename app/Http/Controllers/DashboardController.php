<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'agents' => Auth::user()->organization->agents->load([
                'deals',
                'paidLeaves',
            ]
            )->append([
                'quota_attainment_in_percent',
                'quota_attainment_change',
                'commission',
                'commission_change',
                'sick_leaves_days_count',
                'vacation_leaves_days_count',
                'active_plans',
            ]),
            'open_deal_count' => Deal::whereHas('agents', function (Builder $query) {
                $query->where('organization_id', Auth::user()->organization->id)
                    ->whereNull('agent_deal.accepted_at');
            })->count(),
            'plans' => Plan::whereOrganizationId(Auth::user()->organization->id)->select('id', 'name')->get(),
        ]);
    }
}

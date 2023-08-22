<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\ContinuationOfPayTimeScopeEnum;
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
            'agents' => Auth::user()->organization->agents->load([
                'deals',
                'paidLeaves',
            ]
            )->append([
                'quota_attainment',
                'quota_attainment_change',
                'commission',
                'commission_change',
                'sick_leaves_days_count',
                'vacation_leaves_days_count',
                'active_plans',
            ]),
            'open_deal_count' => Deal::whereNull('accepted_at')->whereHas('agents', function (Builder $query) {
                $query->whereOrganizationId(Auth::user()->organization->id);
            })->count()
        ]);
    }
}

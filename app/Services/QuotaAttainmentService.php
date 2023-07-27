<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use Carbon\Carbon;

class QuotaAttainmentService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): float
    {
        $latestPlanTargetAmountPerMonth = $agent->load('plans')->plans()->active()->first()?->target_amount_per_month;

        if (! $latestPlanTargetAmountPerMonth) {
            return 0;
        }

        return match ($timeScope) {
            TimeScopeEnum::MONTHLY => $agent->deals()->whereMonth('accepted_at', Carbon::now()->month)->sum('value') / $latestPlanTargetAmountPerMonth,
            TimeScopeEnum::QUARTERLY => $agent->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfQuarter(), Carbon::now()->endOfQuarter()])->sum('value') / ($latestPlanTargetAmountPerMonth * 3),
            TimeScopeEnum::ANNUALY => $agent->deals()->whereBetween('accepted_at', [Carbon::now()->firstOfYear(), Carbon::now()->lastOfYear()])->sum('value') / ($latestPlanTargetAmountPerMonth * 12),
            default => $agent->deals()->whereMonth('accepted_at', Carbon::now()->month)->sum('value') / $latestPlanTargetAmountPerMonth,
        };
    }
}

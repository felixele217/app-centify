<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;

class PlanKickerCommissionService
{
    public function calculate(Agent $agent, Plan $plan, TimeScopeEnum $timeScope, ?float $quotaAttainmentThisTimeScope): ?int
    {
        $kicker = $plan?->load('kicker')->kicker;

        if (is_null($quotaAttainmentThisTimeScope) || ! $kicker) {
            return null;
        }

        $factor = $timeScope === TimeScopeEnum::MONTHLY ? 3 : 1;

        $kickerIsMet = $kicker->threshold_in_percent * $factor <= $quotaAttainmentThisTimeScope;

        return $kickerIsMet
            ? intval(round($kicker->payout_in_percent * ($agent->base_salary / 4)))
            : 0;
    }
}

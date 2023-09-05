<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;
use App\Models\Plan;

class PlanKickerCommissionService
{
    public function __construct(
        private TimeScopeEnum $timeScope
    ) {
    }

    public function calculate(Agent $agent, Plan $plan, ?float $quotaAttainmentThisTimeScope): ?int
    {
        $kicker = $plan?->load('kicker')->kicker;

        if (is_null($quotaAttainmentThisTimeScope) || ! $kicker) {
            return null;
        }

        $factor = $this->timeScope === TimeScopeEnum::MONTHLY ? 3 : 1;

        $kickerIsMet = $kicker->threshold_factor * $factor <= $quotaAttainmentThisTimeScope;

        return $kickerIsMet
            ? intval(round($kicker->payout_factor * ($agent->base_salary / 4)))
            : 0;
    }
}

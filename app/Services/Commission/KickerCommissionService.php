<?php

namespace App\Services\Commission;

use App\Models\Agent;

class KickerCommissionService
{
    public function calculate(Agent $agent): int
    {
        if ($agent->plans()->active()->first()?->kicker?->threshold_in_percent <= $agent->quota_attainment) {
            $kickerCommission = $agent->plans()->active()->first()?->kicker?->payout_in_percent * ($agent->base_salary / 12);
        }

        return intval(round($kickerCommission));
    }
}

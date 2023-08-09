<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Models\Agent;

class KickerCommissionService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope, ?float $quotaAttainmentThisTimeScope): ?int
    {
        $kicker = $agent->plans()->active()->first()?->kicker;

        if (is_null($quotaAttainmentThisTimeScope) || ! $kicker) {
            return null;
        }

        $factor = $timeScope === TimeScopeEnum::MONTHLY ? 3 : 1;

        if ($kicker->threshold_in_percent * $factor <= $quotaAttainmentThisTimeScope) {
            $kickerCommission = $kicker->payout_in_percent * ($agent->base_salary / 4);
        }

        return intval(round($kickerCommission ?? 0));
    }
}

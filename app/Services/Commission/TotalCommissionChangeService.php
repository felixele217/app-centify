<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Services\TotalQuotaAttainmentService;

class TotalCommissionChangeService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): ?int
    {
        $quotaAttainmentLastTimeScope = (new TotalQuotaAttainmentService($agent, $timeScope, DateHelper::dateInPreviousTimeScope($timeScope)))->calculate();

        if ($quotaAttainmentLastTimeScope === floatval(0)) {
            return null;
        }

        $commissionLastTimeScope = (new TotalCommissionService($timeScope))->calculate($agent);
        $commissionThisTimeScope = (new TotalCommissionService($timeScope))->calculate($agent);

        return intval($commissionThisTimeScope - $commissionLastTimeScope);
    }
}

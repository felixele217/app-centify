<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Services\QuotaAttainment\TotalQuotaAttainmentService;

class TotalQuotaCommissionChangeService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): ?int
    {
        $quotaAttainmentLastTimeScope = (new TotalQuotaAttainmentService($agent, $timeScope, $dateInPreviousTimeScope = DateHelper::dateInPreviousTimeScope($timeScope)))->calculate();

        if ($quotaAttainmentLastTimeScope === floatval(0)) {
            return null;
        }

        $commissionLastTimeScope = (new TotalQuotaCommissionService($timeScope, $dateInPreviousTimeScope))->calculate($agent);
        $commissionThisTimeScope = (new TotalQuotaCommissionService($timeScope))->calculate($agent);

        return intval($commissionThisTimeScope - $commissionLastTimeScope);
    }
}

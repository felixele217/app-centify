<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Services\QuotaAttainmentService;

class CommissionChangeService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        $commissionThisTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $agent->quota_attainment);

        $quotaAttainmentLastTimeScope = (new QuotaAttainmentService(DateHelper::dateInPreviousTimeScope($timeScope)))->calculate($agent, $timeScope);

        $commissionLastTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentLastTimeScope);

        return intval($commissionThisTimeScope - $commissionLastTimeScope);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use App\Services\QuotaAttainmentService;
use Carbon\CarbonImmutable;

class CommissionChangeService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): int
    {
        $quotaAttainmentThisTimeScope = (new QuotaAttainmentService(CarbonImmutable::now()))->calculate($agent, $timeScope);
        $commissionThisTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentThisTimeScope);

        $quotaAttainmentLastTimeScope = (new QuotaAttainmentService(DateHelper::dateInPreviousTimeScope($timeScope)))->calculate($agent, $timeScope);
        $commissionLastTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentLastTimeScope);


        return intval($commissionThisTimeScope - $commissionLastTimeScope);
    }
}

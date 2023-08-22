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
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): ?int
    {
        $quotaAttainmentLastTimeScope = (new QuotaAttainmentService($agent, $timeScope,DateHelper::dateInPreviousTimeScope($timeScope)))->calculate();

        if (is_null($quotaAttainmentLastTimeScope)) {
            return null;
        }

        $commissionLastTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentLastTimeScope);

        $quotaAttainmentThisTimeScope = (new QuotaAttainmentService($agent, $timeScope, CarbonImmutable::now()))->calculate();
        $commissionThisTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentThisTimeScope);

        return intval($commissionThisTimeScope - $commissionLastTimeScope);
    }
}

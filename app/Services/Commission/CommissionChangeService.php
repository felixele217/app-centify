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
        $quotaAttainmentLastTimeScope = (new QuotaAttainmentService(DateHelper::dateInPreviousTimeScope($timeScope)))->calculate($agent, $timeScope);

        if (is_null($quotaAttainmentLastTimeScope)) {
            return null;
        }

        $commissionLastTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentLastTimeScope);

        $quotaAttainmentThisTimeScope = (new QuotaAttainmentService(CarbonImmutable::now()))->calculate($agent, $timeScope);
        $commissionThisTimeScope = (new CommissionFromQuotaService())->calculate($agent, $timeScope, $quotaAttainmentThisTimeScope);

        return intval($commissionThisTimeScope - $commissionLastTimeScope);
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use Carbon\CarbonImmutable;

class TotalQuotaAttainmentChangeService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): ?float
    {
        $dateInPreviousTimeScope = DateHelper::dateInPreviousTimeScope($timeScope);

        $quotaAttainmentPreviousTimeScope = (new TotalQuotaAttainmentService($agent, $timeScope, $dateInPreviousTimeScope))->calculate();

        if ($quotaAttainmentPreviousTimeScope === floatval(0)) {
            return null;
        }

        $quotaAttainmentThisTimeScope = (new TotalQuotaAttainmentService($agent, $timeScope, CarbonImmutable::now()))->calculate();

        return $quotaAttainmentThisTimeScope - $quotaAttainmentPreviousTimeScope;
    }
}

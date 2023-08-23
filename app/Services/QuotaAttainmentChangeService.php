<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\TimeScopeEnum;
use App\Helper\DateHelper;
use App\Models\Agent;
use Carbon\CarbonImmutable;

class QuotaAttainmentChangeService
{
    public function calculate(Agent $agent, TimeScopeEnum $timeScope): ?float
    {
        $dateInPreviousTimeScope = DateHelper::dateInPreviousTimeScope($timeScope);

        $quotaAttainmentPreviousTimeScope = (new QuotaAttainmentService($agent, $timeScope,$dateInPreviousTimeScope))->calculate();

        if (is_null($quotaAttainmentPreviousTimeScope)) {
            return null;
        }

        $quotaAttainmentThisTimeScope = (new QuotaAttainmentService($agent, $timeScope,CarbonImmutable::now()))->calculate();

        return $quotaAttainmentThisTimeScope - $quotaAttainmentPreviousTimeScope;
    }
}

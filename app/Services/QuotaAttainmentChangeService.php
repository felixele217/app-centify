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

        $quotaAttainmentPreviousTimeScope = (new QuotaAttainmentService($dateInPreviousTimeScope))->calculate($agent, $timeScope);

        if ($quotaAttainmentPreviousTimeScope === floatval(0)) {
            return null;
        }

        $quotaAttainmentThisTimeScope = (new QuotaAttainmentService(CarbonImmutable::now()))->calculate($agent, $timeScope);

        return $quotaAttainmentThisTimeScope - $quotaAttainmentPreviousTimeScope;
    }
}

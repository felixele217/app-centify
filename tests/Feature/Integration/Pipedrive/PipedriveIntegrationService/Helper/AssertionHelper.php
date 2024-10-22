<?php

namespace Tests\Feature\Integration\Pipedrive\PipedriveIntegrationService\Helper;

use App\Enum\TriggerEnum;
use App\Integrations\Pipedrive\PipedriveHelper;

class AssertionHelper
{
    public static function dealsCountForAllTriggers(string $email, array $deals): int
    {
        $dealsForAgentCount = 0;

        foreach ($deals as $deal) {

            if (PipedriveHelper::wonDeal($email, $deal) || self::setDemoForDeal($email, $deal)) {
                $dealsForAgentCount++;
            }
        }

        return $dealsForAgentCount;
    }

    public static function dealsCountForTrigger(string $email, array $deals, TriggerEnum $trigger): int
    {
        $dealsCount = 0;

        foreach ($deals as $deal) {
            if ($trigger === TriggerEnum::DEAL_WON && PipedriveHelper::wonDeal($email, $deal)) {
                $dealsCount++;
            }

            if ($trigger === TriggerEnum::DEMO_SCHEDULED && self::setDemoForDeal($email, $deal)) {
                $dealsCount++;
            }
        }

        return $dealsCount;
    }

    private static function setDemoForDeal(string $email, array $deal): bool
    {

        return PipedriveHelper::demoSetByEmail($deal, env('PIPEDRIVE_DEMO_SET_BY')) === $email;
    }
}

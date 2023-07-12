<?php

namespace App\Integrations\Pipedrive;

use App\Enum\CustomIntegrationFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Facades\Pipedrive;
use App\Helper\DateHelper;
use App\Integrations\IntegrationServiceContract;
use App\Models\Agent;
use Illuminate\Support\Facades\Auth;

class PipedriveIntegrationService implements IntegrationServiceContract
{
    public static function agentDeals(array $deals = null): array
    {
        $deals = json_decode(json_encode(Pipedrive::deals()->all()->getData()), true);

        $agentDeals = [];

        foreach (self::agentEmails($deals) as $email) {
            array_push($agentDeals, self::dealsForAgent($email, $deals));
        }

        return $agentDeals;
    }

    private static function agentEmails(array $deals): array
    {
        $agentEmails = [];

        foreach ($deals as $deal) {

            $email = PipedriveHelper::demoSetByEmail($deal);

            if ($email && ! in_array($email, $agentEmails)) {
                array_push($agentEmails, $email);
            }
        }

        return $agentEmails;
    }

    public static function dealsForAgent(string $agentEmail, array $deals): array
    {
        $demoSetByApiKey = Auth::user()->organization->customIntegrationFields()
            ->whereIntegrationType(IntegrationTypeEnum::PIPEDRIVE->value)
            ->whereName(CustomIntegrationFieldEnum::DEMO_SET_BY->value)
            ->first()?->api_key;

        return [
            $agentEmail => collect($deals)
                ->filter(function ($deal) use ($agentEmail, $demoSetByApiKey) {
                    return $agentEmail === PipedriveHelper::demoSetByEmail($deal, $demoSetByApiKey) && isset($deal[$demoSetByApiKey]);
                })
                ->map(function ($deal) use ($demoSetByApiKey) {
                    $demoSetBy = $demoSetByApiKey ? $deal[$demoSetByApiKey]['email'][0]['value'] : null;

                    return array_filter([
                        'id' => $deal['id'],
                        'title' => $deal['title'],
                        'value' => $deal['value'],
                        'add_time' => $deal['add_time'],
                        'status' => $deal['status'],
                        'owner_email' => $demoSetBy,
                    ], function ($value) {
                        return $value !== null;
                    });
                }),
        ];
    }

    public static function syncAgentDeals(): void
    {
        $agentDeals = self::agentDeals();

        foreach ($agentDeals as $agentDeals) {
            foreach ($agentDeals as $email => $deals) {
                foreach ($deals as $deal) {
                    Agent::whereEmail($email)->first()?->deals()->updateOrCreate(
                        [
                            'integration_deal_id' => $deal['id'],
                            'integration_type' => IntegrationTypeEnum::PIPEDRIVE->value,
                        ],
                        [
                            'integration_deal_id' => $deal['id'],
                            'integration_type' => IntegrationTypeEnum::PIPEDRIVE->value,
                            'title' => $deal['title'],
                            'value' => $deal['value'] * 100,
                            'add_time' => DateHelper::parsePipedriveTime($deal['add_time']),
                            'status' => $deal['status'],
                            'owner_email' => $deal['owner_email'],
                        ]
                    );
                }
            }
        }
    }
}

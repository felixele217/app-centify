<?php

namespace App\Integrations\Pipedrive;

use App\Enum\IntegrationEnum;
use App\Facades\Pipedrive;
use App\Integrations\IntegrationServiceContract;
use App\Models\Agent;

class PipedriveIntegrationService implements IntegrationServiceContract
{
    public static function agentDeals(array $deals = null): array
    {
        $deals = json_decode(json_encode(Pipedrive::deals()->all()->getData()), true);

        $agentDeals = [];

        foreach (self::agentEmails($deals) as $email) {
            array_push($agentDeals, self::dealsForAgent($email, $deals));
        }

        return $agentDeals[0];
    }

    private static function agentEmails(array $deals): array
    {
        $agentEmails = [];

        foreach ($deals as $deal) {

            $email = $deal['creator_user_id']['email'];

            if (! in_array($email, $agentEmails)) {
                array_push($agentEmails, $email);
            }
        }

        return $agentEmails;
    }

    public static function dealsForAgent(string $agentEmail, array $deals): array
    {
        return [
            $agentEmail => collect($deals)->map(function ($deal) use ($agentEmail) {
                if ($agentEmail === $deal['creator_user_id']['email']) {
                    return [
                        'id' => $deal['id'],
                        'title' => $deal['title'],
                        'target_amount' => $deal['value'],
                        'add_time' => $deal['add_time'],
                        'status' => $deal['status'],
                    ];
                }
            }),
        ];
    }

    public static function syncAgentDeals(): void
    {
        $agentDeals = self::agentDeals();

        foreach ($agentDeals as $email => $deals) {
            foreach ($deals as $deal) {
                Agent::whereEmail($email)->first()->deals()->create([
                    'integration_id' => $deal['id'],
                    'integration_type' => IntegrationEnum::PIPEDRIVE->value,
                    'title' => $deal['title'],
                    'target_amount' => $deal['target_amount'],
                    'add_time' => $deal['add_time'],
                    'status' => $deal['status'],
                ]);
            }
        }
    }
}

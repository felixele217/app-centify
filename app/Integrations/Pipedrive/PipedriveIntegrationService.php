<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Facades\Pipedrive;
use App\Helper\DateHelper;
use App\Integrations\IntegrationServiceContract;
use App\Models\Agent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PipedriveIntegrationService implements IntegrationServiceContract
{
    private string $demoSetByApiKey;

    public function __construct()
    {
        $this->demoSetByApiKey = Auth::user()->organization->integrations()
            ->whereName(IntegrationTypeEnum::PIPEDRIVE->value)
            ->first()
            ->customFields()
            ->whereName(CustomFieldEnum::DEMO_SET_BY->value)
            ->first()?->api_key;
    }

    public function agentDeals(array $deals = null): array
    {
        $deals = json_decode(json_encode(Pipedrive::deals()->all()->getData()), true);

        $agentDeals = [];

        foreach ($this->agentEmails($deals) as $email) {
            array_push($agentDeals, $this->dealsForAgent($email, $deals));
        }

        return $agentDeals;
    }

    private function agentEmails(array $deals): array
    {
        $agentEmails = [];

        foreach ($deals as $deal) {
            $email = PipedriveHelper::demoSetByEmail($deal, $this->demoSetByApiKey);

            if ($email && ! in_array($email, $agentEmails)) {
                array_push($agentEmails, $email);
            }
        }

        return $agentEmails;
    }

    public function dealsForAgent(string $agentEmail, array $deals): array
    {
        return [
            $agentEmail => collect($deals)
                ->filter(function (array $deal) use ($agentEmail) {
                    return $agentEmail === PipedriveHelper::demoSetByEmail($deal, $this->demoSetByApiKey) && isset($deal[$this->demoSetByApiKey]);
                })
                ->map(function (array $deal) {
                    $demoSetBy = $this->demoSetByApiKey ? $deal[$this->demoSetByApiKey]['email'][0]['value'] : null;

                    return array_filter([
                        'id' => $deal['id'],
                        'title' => $deal['title'],
                        'value' => $deal['value'],
                        'add_time' => $deal['add_time'],
                        'status' => $deal['status'],
                        'owner_email' => $demoSetBy,
                    ], function (string $value) {
                        return $value !== null;
                    });
                }),
        ];
    }

    public function syncAgentDeals(): void
    {
        $agentDeals = $this->agentDeals();

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

        Auth::user()->organization->integrations()->whereName(IntegrationTypeEnum::PIPEDRIVE->value)->first()->update([
            'last_synced_at' => Carbon::now(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Exceptions\SyncWithoutConnectionException;
use App\Facades\PipedriveFacade;
use App\Helper\DateHelper;
use App\Integrations\IntegrationServiceContract;
use App\Models\Agent;
use App\Models\Integration;
use App\Models\Organization;
use Carbon\Carbon;

class PipedriveIntegrationService implements IntegrationServiceContract
{
    private PipedriveFacade $pipedriveClient;

    private Integration $pipedriveIntegration;

    private ?string $demoSetByApiKey;

    public function __construct(private Organization $organization)
    {
        $this->pipedriveClient = new PipedriveFacade($organization);

        if ($pipedriveIntegration = $organization
            ->integrations()
            ->whereName(IntegrationTypeEnum::PIPEDRIVE->value)
            ->first()) {
            $this->pipedriveIntegration = $pipedriveIntegration;
        } else {
            throw new SyncWithoutConnectionException();
        }

        $this->demoSetByApiKey = $this->pipedriveIntegration
            ->customFields()
            ->whereName(CustomFieldEnum::DEMO_SET_BY->value)
            ->first()?->api_key;
    }

    public function agentDeals(array $deals = null): array
    {
        $deals = $this->pipedriveClient->deals();

        $agentDeals = [];

        foreach ($this->organization->agents as $agent) {
            array_push($agentDeals, $this->dealsForAgent($agent, $deals));
        }

        return $agentDeals;
    }

    public function dealsForAgent(Agent $agent, array $deals): array
    {
        return [
            $agent->email => collect($deals)
                ->filter(function (array $deal) use ($agent) {
                    if ($agent->plans()->active()->first()?->trigger->value === TriggerEnum::DEAL_WON->value) {
                        return $agent->email === PipedriveHelper::ownerEmail($deal);
                    }

                    if ($agent->plans()->active()->first()?->trigger->value === TriggerEnum::DEMO_SET_BY->value) {
                        return $agent->email === PipedriveHelper::demoSetByEmail($deal, $this->demoSetByApiKey) && isset($deal[$this->demoSetByApiKey]);
                    }

                    return false;
                })
                ->map(function (array $deal) {
                    return array_filter([
                        'id' => $deal['id'],
                        'title' => $deal['title'],
                        'value' => $deal['value'],
                        'add_time' => $deal['add_time'],
                        'status' => $deal['status'],
                        'owner_email' => PipedriveHelper::demoSetByEmail($deal, $this->demoSetByApiKey),
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

        $this->pipedriveIntegration->update([
            'last_synced_at' => Carbon::now(),
        ]);
    }
}

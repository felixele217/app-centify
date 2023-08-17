<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
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

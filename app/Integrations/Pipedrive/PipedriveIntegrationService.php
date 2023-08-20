<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Exceptions\SyncWithoutConnectionException;
use App\Facades\PipedriveFacade;
use App\Integrations\IntegrationServiceContract;
use App\Models\Agent;
use App\Models\Integration;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
            array_push($agentDeals, [$agent->email => $this->dealsForAgent($agent, $deals)]);
        }

        return $this->transformAgentDealsArray($agentDeals);
    }

    public function dealsForAgent(Agent $agent, array $integrationDeals): Collection
    {
        return collect($integrationDeals)
            ->filter(fn (array $integrationDealArray) => $this->shouldBeSyncedForThisAgent($agent, $integrationDealArray))
            ->map(fn (array $integrationDealArray) => new PipedriveDealDTO($integrationDealArray));
    }

    private function shouldBeSyncedForThisAgent(Agent $agent, array $integrationDealArray): bool
    {
        foreach ($agent->plans()->active()->get() as $plan) {
            if ($plan->trigger->value === TriggerEnum::DEMO_SET_BY->value
            && $agent->email === PipedriveHelper::demoSetByEmail($integrationDealArray, $this->demoSetByApiKey)) {
                return true;
            }

            if ($plan->trigger->value === TriggerEnum::DEAL_WON->value
            && PipedriveHelper::wonDeal($agent->email, $integrationDealArray)) {
                return true;
            }
        }

        return false;
    }

    public function syncAgentDeals(): void
    {
        $agentDealsDTOs = $this->agentDeals();

        foreach ($agentDealsDTOs as $email => $dealDTOs) {
            foreach ($dealDTOs as $dealDTO) {
                Agent::whereEmail($email)->first()?->deals()->updateOrCreate(
                    $dealDTO->integrationIdentifiers(),
                    $dealDTO->toArray(),
                );
            }
        }

        $this->pipedriveIntegration->update([
            'last_synced_at' => Carbon::now(),
        ]);
    }

    private function transformAgentDealsArray(array $agentDeals): array
    {
        $transformedAgentDeals = [];

        foreach ($agentDeals as $agentDeal) {
            $email = array_keys($agentDeal)[0];

            if ($agentDeal[$email]->count()) {
                $transformedAgentDeals[$email] = $agentDeal[$email];
            }
        }

        return $transformedAgentDeals;
    }
}

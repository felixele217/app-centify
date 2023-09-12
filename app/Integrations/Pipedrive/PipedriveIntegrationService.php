<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Enum\TriggerEnum;
use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\SyncWithoutConnectionException;
use App\Facades\PipedriveFacade;
use App\Helper\DateHelper;
use App\Integrations\IntegrationServiceContract;
use App\Models\Agent;
use App\Models\AgentDeal;
use App\Models\Deal;
use App\Models\Integration;
use App\Models\Organization;
use App\Models\Plan;
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

        if (! $this->demoSetByApiKey) {
            throw new InvalidApiKeyException();
        }
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
            ->map(fn (array $integrationDealArray) => new PipedriveDealDTO(
                $integrationDealArray,
                PipedriveHelper::scheduledDemoForDeal($agent->email, $integrationDealArray, $this->demoSetByApiKey),
                PipedriveHelper::wonDeal($agent->email, $integrationDealArray)
            ));
    }

    private function shouldBeSyncedForThisAgent(Agent $agent, array $integrationDealArray): bool
    {
        foreach ($agent->plans()->active()->get() as $plan) {
            if ($plan->trigger->value === TriggerEnum::DEMO_SCHEDULED->value
            && PipedriveHelper::scheduledDemoForDeal($agent->email, $integrationDealArray, $this->demoSetByApiKey)) {
                return $this->addedAfterPlanStartDate($plan, $integrationDealArray);
            }

            if ($plan->trigger->value === TriggerEnum::DEAL_WON->value
            && PipedriveHelper::wonDeal($agent->email, $integrationDealArray)) {
                return $this->addedAfterPlanStartDate($plan, $integrationDealArray);
            }
        }

        return false;
    }

    public function syncAgentDeals(): void
    {
        $agentDealsDTOs = $this->agentDeals();

        foreach ($agentDealsDTOs as $email => $dealDTOs) {
            foreach ($dealDTOs as $dealDTO) {
                $deal = Deal::updateOrCreate(
                    $dealDTO->integrationIdentifiers(),
                    $dealDTO->toArray(),
                );

                if ($dealDTO->scheduledDemo) {
                    AgentDeal::firstOrCreate([
                        'agent_id' => Agent::whereEmail($email)->first()?->id,
                        'deal_id' => $deal->id,
                        'triggered_by' => TriggerEnum::DEMO_SCHEDULED->value,
                    ], [
                        'accepted_at' => $this->organization->auto_accept_demo_scheduled ? Carbon::now() : null,
                    ]);
                }

                if ($dealDTO->wonDeal) {
                    AgentDeal::firstOrCreate([
                        'agent_id' => Agent::whereEmail($email)->first()?->id,
                        'deal_id' => $deal->id,
                        'triggered_by' => TriggerEnum::DEAL_WON->value,
                    ], [
                        'accepted_at' => $this->organization->auto_accept_deal_won ? Carbon::now() : null,
                    ]);
                }
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

    private function addedAfterPlanStartDate(Plan $plan, array $integrationDealArray): bool
    {
        return DateHelper::parsePipedriveTime($integrationDealArray['add_time'])->gt($plan->start_date);
    }
}

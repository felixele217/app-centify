<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enum\IntegrationTypeEnum;
use App\Integrations\Pipedrive\PipedriveIntegrationService;
use App\Models\Organization;
use Illuminate\Console\Command;

class SyncPipedriveCommand extends Command
{
    protected $signature = 'sync-pipedrive';

    protected $description = 'Synchronizes the pipedrive data for all organizations that have an active pipedrive integration.';

    public function handle(): void
    {
        foreach (Organization::all() as $organization) {
            if ($organization->integrations()->whereName(IntegrationTypeEnum::PIPEDRIVE->value)->first()) {
                (new PipedriveIntegrationService($organization))->syncAgentDeals();
            }
        }

    }
}

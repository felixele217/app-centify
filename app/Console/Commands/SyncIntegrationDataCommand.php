<?php

namespace App\Console\Commands;

use App\Integrations\Pipedrive\PipedriveIntegrationService;
use Illuminate\Console\Command;

class SyncIntegrationDataCommand extends Command
{
    protected $signature = 'sync-integration-data';

    protected $description = 'Synchronizes the data of all integrations to our internal database.';

    public function handle(): void
    {
        PipedriveIntegrationService::syncAgentDeals();
    }
}

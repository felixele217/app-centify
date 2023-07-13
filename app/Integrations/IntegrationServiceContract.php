<?php

namespace App\Integrations;

interface IntegrationServiceContract
{
    public function agentDeals(): array;

    public function syncAgentDeals(): void;
}

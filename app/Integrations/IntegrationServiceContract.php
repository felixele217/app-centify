<?php

declare(strict_types=1);

namespace App\Integrations;

interface IntegrationServiceContract
{
    public function agentDeals(): array;

    public function syncAgentDeals(): void;
}

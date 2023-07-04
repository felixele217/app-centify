<?php

namespace App\Integrations;

interface IntegrationServiceContract
{
    public static function agentDeals(): array;

    public static function syncAgentDeals(): void;
}

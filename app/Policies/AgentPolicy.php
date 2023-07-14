<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;
use App\Models\Agent;

class AgentPolicy
{
    public function any(Admin $admin, Agent $agent): bool
    {
        return $admin->organization->id === $agent->organization->id;
    }
}

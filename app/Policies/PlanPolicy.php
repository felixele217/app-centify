<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Plan;

class PlanPolicy
{
    public function any(Admin $admin, Plan $plan): bool
    {
        return $admin->organization->id === $plan->organization->id;
    }
}

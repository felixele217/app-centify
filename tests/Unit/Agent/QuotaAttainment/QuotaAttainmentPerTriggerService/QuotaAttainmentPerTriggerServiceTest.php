<?php

use App\Enum\TimeScopeEnum;
use App\Enum\TriggerEnum;
use App\Models\Agent;
use App\Services\QuotaAttainmentPerTriggerService;

it('returns null if the agent has no active plan with the passed trigger', function (TriggerEnum $trigger) {
    signInAdmin();

    expect((new QuotaAttainmentPerTriggerService(Agent::factory()->create(), $trigger, TimeScopeEnum::MONTHLY))->calculate())->toBeNull();
})->with(TriggerEnum::cases());

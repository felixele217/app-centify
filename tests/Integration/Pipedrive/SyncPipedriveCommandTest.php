<?php

use App\Enum\CustomFieldEnum;
use App\Integrations\Pipedrive\PipedriveClientDummy;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Models\Agent;
use App\Models\CustomField;
use App\Models\Integration;
use App\Models\Organization;
use Illuminate\Support\Facades\Artisan;

it('sync correctly using the command', function () {
    $organization = Organization::factory()->create();

    $integration = Integration::factory()->create([
        'organization_id' => $organization->id,
    ]);

    CustomField::factory()
        ->ofIntegration($integration->id)
        ->create([
            'api_key' => env('PIPEDRIVE_DEMO_SET_BY'),
            'name' => CustomFieldEnum::DEMO_SET_BY->value,
        ]);

    $deals = (new PipedriveClientDummy())->deals()->toArray();
    $email = PipedriveHelper::demoSetByEmail($deals[0]);
    $agent = Agent::factory()->create([
        'email' => $email,
        'organization_id' => $organization->id,
    ]);

    Artisan::call('sync-pipedrive');

    expect($agent->deals->count())->not()->toBe(0);
});

it('executes the command daily', function () {
    $this->artisan('schedule:list')->expectsOutputToContain('0 6 * * *  php artisan sync-pipedrive');
});

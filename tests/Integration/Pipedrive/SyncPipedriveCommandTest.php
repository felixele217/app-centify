<?php

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Integrations\Pipedrive\Mocking\PipedriveClientDummy;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Models\Agent;
use App\Models\CustomField;
use App\Models\Integration;
use Illuminate\Support\Facades\Artisan;

it('sync correctly using the command', function () {
    $admin = signInAdmin();

    $integration = Integration::factory()->create([
        'organization_id' => $admin->organization->id,
        'name' => IntegrationTypeEnum::PIPEDRIVE->value,
    ]);

    CustomField::create([
        'name' => CustomFieldEnum::DEMO_SET_BY->value,
        'integration_id' => $integration->id,
        'api_key' => env('PIPEDRIVE_DEMO_SET_BY', 'invalid key'),
    ]);

    $deals = (new PipedriveClientDummy($admin->organization))->deals()->toArray();
    $email = PipedriveHelper::demoSetByEmail($deals[0], env('PIPEDRIVE_DEMO_SET_BY'));
    $agent = Agent::factory()->create([
        'email' => $email,
        'organization_id' => $admin->organization->id,
    ]);

    Artisan::call('sync-pipedrive');

    expect($agent->deals->count())->not()->toBe(0);
});

it('executes the command daily', function () {
    $this->artisan('schedule:list')->expectsOutputToContain('0 6 * * *  php artisan sync-pipedrive');
});

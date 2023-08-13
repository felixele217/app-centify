<?php

use App\Enum\CustomFieldEnum;
use App\Enum\IntegrationTypeEnum;
use App\Exceptions\InvalidApiKeyException;
use App\Facades\PipedriveFacade;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Models\CustomField;
use App\Models\Integration;

beforeEach(function () {
    $this->admin = signInAdmin();

    $integration = Integration::factory()->create([
        'organization_id' => $this->admin->organization->id,
        'name' => IntegrationTypeEnum::PIPEDRIVE->value,
    ]);

    CustomField::create([
        'name' => CustomFieldEnum::DEMO_SET_BY->value,
        'integration_id' => $integration->id,
        'api_key' => env('PIPEDRIVE_DEMO_SET_BY', 'invalid key'),
    ]);

    $this->pipedriveClient = new PipedriveFacade($this->admin->organization);
});

it('returns demo_set_by email value as agent_email', function () {
    $deals = $this->pipedriveClient->deals();

    expect(PipedriveHelper::demoSetByEmail($deals[0]))->toBe($deals[0][env('PIPEDRIVE_DEMO_SET_BY')]['email'][0]['value']);
});

it('returns null if it has no demo_set_by email', function () {
    $deals = $this->pipedriveClient->deals();

    expect(PipedriveHelper::demoSetByEmail($deals[2]))->toBe(null);
});

it('throws an exception if the provided api key is wrong', function () {
    $deals = $this->pipedriveClient->deals();

    PipedriveHelper::demoSetByEmail($deals[2], 'invalid api key');
})->throws(InvalidApiKeyException::class);

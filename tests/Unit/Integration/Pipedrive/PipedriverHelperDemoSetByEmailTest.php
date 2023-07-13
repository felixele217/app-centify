<?php

use App\Exceptions\InvalidApiKeyException;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Integrations\Pipedrive\PipedriveClientDummy;

it('returns demo_set_by email value as agent_email', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    expect(PipedriveHelper::demoSetByEmail($deals[0]))->toBe($deals[0][env('PIPEDRIVE_DEMO_SET_BY')]['email'][0]['value']);
});

it('returns null if it has no demo_set_by email', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    expect(PipedriveHelper::demoSetByEmail($deals[2]))->toBe(null);
});

it('throws an exception if the provided api key is wrong', function () {
    $deals = (new PipedriveClientDummy())->deals()->toArray();

    expect(PipedriveHelper::demoSetByEmail($deals[2], 'invalid api key'))->toBe(null);
})->throws(InvalidApiKeyException::class, 'Invalid api key!');

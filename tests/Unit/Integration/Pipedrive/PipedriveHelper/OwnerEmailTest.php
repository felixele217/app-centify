<?php

use App\Facades\PipedriveFacade;
use App\Integrations\Pipedrive\PipedriveHelper;

beforeEach(function () {
    $this->admin = signInAdmin();

    $this->pipedriveClient = new PipedriveFacade($this->admin->organization);
});

it('returns owner email value', function () {
    $deals = $this->pipedriveClient->deals();

    expect(PipedriveHelper::ownerEmail($deals[0]))->toBe($deals[0]['person_id']['email'][0]['value']);
    expect(PipedriveHelper::ownerEmail($deals[2]))->toBe($deals[2]['person_id']['email']);
});

<?php

use App\Integrations\Pipedrive\PipedriveClientDummy;

it('returns correct dealCount', function () {
    $deals = (new PipedriveClientDummy())->deals()['data'];

    $email = $deals[0]['creator_user_id']['email'];

    expect(PipedriveClientDummy::dealCount($email))->toBe(3);
});

<?php

use App\Helper\DateHelper;

it('correctly parses pipedrive times to carbon instances', function () {
    $pipedriveTime = '2023-06-26 08 =>33 =>25';

    expect(DateHelper::parsePipedriveTime($pipedriveTime)->toDateTimeString())->toBe('2023-06-26 08:33:25');
});

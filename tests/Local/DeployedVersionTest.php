<?php

it('returns content of deployed_version.txt file as a json', function () {
    $filePath = base_path('storage/app/deployed_version.txt');

    file_put_contents($filePath, [
        '#123456',
    ]);

    $contents = file_get_contents($filePath);

    $version = $this->getJson(route('deployed-version'))['version'];

    expect($version)->toBe($contents);
});

it('trims the \n char of the version', function () {
    $filePath = base_path('storage/app/deployed_version.txt');

    file_put_contents($filePath, [
        "#123456\n",
    ]);

    $versionFromFile = file_get_contents($filePath);

    $versionFromRoute = $this->getJson(route('deployed-version'))['version'];

    expect($versionFromRoute)->toBe(trim($versionFromFile));
});

<?php

use Database\Seeders\TruncateTables;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

it('does not truncate specific tables', function () {
    Artisan::call('db:seed --class=TestDataSeeder');

    $TruncateTables = new TruncateTables();
    $TruncateTables->run();

    foreach ($TruncateTables->truncatedTables() as $truncatedTable) {
        expect(DB::table($truncatedTable)->count())->toBe(0);
    }
});

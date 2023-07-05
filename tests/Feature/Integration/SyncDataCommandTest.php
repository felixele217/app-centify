<?php

use App\Console\Commands\SyncIntegrationDataCommand;
use App\Integrations\Pipedrive\PipedriveClientDummy;
use App\Models\Agent;

it('synchronizes pipedrive data properly', function () {
    $email = (new PipedriveClientDummy())->deals()->toArray()[0]['creator_user_id']['email'];

    $emailCount = PipedriveClientDummy::dealCount($email);

    $agent = Agent::factory()->create([
        'email' => $email,
    ]);

    $this->artisan(SyncIntegrationDataCommand::class);

    expect($agent->deals)->toHaveCount($emailCount);
});

it('executes the command daily', function () {
    $this->artisan('schedule:list')->expectsOutputToContain('5 0 * * *  php artisan sync-integration-data');
});

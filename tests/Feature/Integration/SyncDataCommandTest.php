<?php

use App\Models\User;
use App\Console\Commands\SyncIntegrationDataCommand;
use App\Integrations\Pipedrive\PipedriveClientDummy;

it('synchronizes pipedrive data properly', function () {
    $email = (new PipedriveClientDummy())->deals()['data'][0]['creator_user_id']['email'];

    $emailCount = PipedriveClientDummy::dealCount($email);

    $agent = User::factory()->agent()->create([
        'email' => $email,
    ]);

    $this->artisan(SyncIntegrationDataCommand::class);

    expect($agent->deals)->toHaveCount($emailCount);
});

it('executes the command daily', function () {
    $this->artisan('schedule:list')->expectsOutputToContain('5 0 * * *  php artisan mail:send-reminder-after-deadline-expired-command');
})->todo();

<?php

it('can update auto-access fields', function () {
    $admin = signInAdmin();

    $this->put(route('profile.edit'), [
        'auto_accept_demo_scheduled' => true,
        'auto_accept_deal_won' => true,
    ])->assertRedirect();

    expect($admin->organization->auto_accept_demo_scheduled)->toBeTrue();
    expect($admin->organization->auto_accept_deal_won)->toBeTrue();
});

<?php

use App\Models\Deal;

use function Pest\Laravel\withoutExceptionHandling;

it('stores the correct rejection upon rejecting a deal', function (bool $isPermanent) {
    signInAdmin();

    $deal = Deal::factory()->create();

    $this->post(route('deals.rejections.store', $deal), [
        'rejection_reason' => $reason = 'quality was poor',
        'is_permanent' => $isPermanent,
    ])->assertRedirect();

    $rejection = $deal->fresh()->rejections->first();

    expect($rejection->reason)->toBe($reason);
    expect($rejection->is_permanent)->toBe($isPermanent);
})->with([
    true, false,
]);

it('reason is required for declining a deal', function () {
    signInAdmin();

    $deal = Deal::factory()->create();

    $this->post(route('deals.rejections.store', $deal), [
        'rejection_reason' => null,
        'is_permanent' => null,
    ])->assertInvalid([
        'rejection_reason' => 'You must provide a reason.',
    ]);
});

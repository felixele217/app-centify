<?php

use App\Enum\DealScopeEnum;
use App\Models\Agent;
use App\Models\Deal;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->admin = signInAdmin();

    Deal::factory($this->openDealCount = 2)->create([
        'agent_id' => Agent::factory()->create([
            'organization_id' => $this->admin->organization->id,
        ]),
    ]);

    Deal::factory($this->acceptedDealCount = 3)->create([
        'agent_id' => Agent::factory()->create([
            'organization_id' => $this->admin->organization->id,
        ]),
        'accepted_at' => Carbon::now(),
    ]);

    Deal::factory($this->declinedDealCount = 4)->create([
        'agent_id' => Agent::factory()->create([
            'organization_id' => $this->admin->organization->id,
        ]),
        'declined_at' => Carbon::now(),
    ]);

    Deal::factory($this->foreignDealCount = 1)->create();
});

it('passes the correct props for scope=all', function (string $query) {
    $this->get(route('deals.index').$query)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', $this->openDealCount + $this->acceptedDealCount + $this->declinedDealCount)
            ->has('deals.1.agent')
    );
})->with([
    '?scope='.DealScopeEnum::ALL->value,
    '',
])->skip();

it('passes the correct props for scope=open', function () {
    $this->get(route('deals.index') . '?scope=1' . DealScopeEnum::OPEN->value)->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Deal/Index')
            ->has('deals', $this->openDealCount)
            ->has('deals.1.agent')
    );
});

// it('passes the correct props for scope=accepted', function () {

// });

// it('passes the correct props for scope=declined', function () {

// });

// it('does not send foreign deals', function () {
//     signInAdmin();

//     Deal::factory(5)->create();

//     $this->get(route('deals.index'))->assertInertia(
//         fn (AssertableInertia $page) => $page
//             ->component('Deal/Index')
//             ->has('deals', 0)
//     );
// });

// it('only sends not accepted deals', function () {
//     $admin = signInAdmin();

//     Deal::factory(4)->create([
//         'agent_id' => $agent = Agent::factory()->create([
//             'organization_id' => $admin->organization->id,
//         ]),
//         'accepted_at' => Carbon::now(),
//     ]);

//     Deal::factory($notAcceptedDealsCount = 2)->create([
//         'agent_id' => $agent->id,
//         'accepted_at' => null,
//     ]);

//     $this->get(route('deals.index'))->assertInertia(
//         fn (AssertableInertia $page) => $page
//             ->component('Deal/Index')
//             ->has('deals', $notAcceptedDealsCount)
//     );
// });

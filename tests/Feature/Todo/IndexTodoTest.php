<?php

use Inertia\Testing\AssertableInertia;

it('passes the correct props', function () {
    signIn();

    $this->get(route('todos.index'))->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Todo/Index')
    );
});

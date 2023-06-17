<?php

it('test', function () {
    $this->get(route('authenticate'));

    $this->get(route('test'));
});

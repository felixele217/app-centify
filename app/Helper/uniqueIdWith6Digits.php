<?php

function uniqueIdWith6Digits(): int
{
    $id = fake()->unique()->randomNumber(6);

    return $id;
}

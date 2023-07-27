<?php

test('traits folder are all traits')
    ->expect('App\Traits')
    ->toBeTraits();

test('enum folder are all enums')
    ->expect('App\Enum')
    ->toBeEnums();

test('models all extend eloquent base model')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model');

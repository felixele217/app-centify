<?php

test('always uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

test('traits folder are all traits')
    ->expect('App\Traits')
    ->toBeTraits();

test('dont use globals')
    ->expect(['dd', 'dump'])
    ->not()->toBeUsed();

test('controllers do not use base request class')
    ->expect('Illuminate\Http\Request')
    ->not()->toBeUsedIn('App\Http\Controllers');

test('enum folder are all enums')
    ->expect('App\Enum')
    ->toBeEnums();

test('models all extend eloquent base model')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model');

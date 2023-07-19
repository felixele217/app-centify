<?php

use App\Encrypter;

it('encrypts and decrypts properly', function () {
    $value = 'some4token4to4be4encrypted';

    $encryptedValue = Encrypter::encrypt($value);

    $decryptedValue = Encrypter::decrypt($encryptedValue);

    expect($encryptedValue)->not()->toBe($value);
    expect($decryptedValue)->toBe($value);
});

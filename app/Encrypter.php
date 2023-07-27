<?php

declare(strict_types=1);

namespace App;

class Encrypter
{
    const CIPHER = 'aes-256-gcm';

    const TAG_LENGTH = 16;

    public static function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes(self::getIvLen());
        $tag = null;

        $encryptedData = openssl_encrypt($data, self::CIPHER, env('ENCRYPTION_KEY'), $options = OPENSSL_RAW_DATA, $iv, $tag, '', self::TAG_LENGTH);

        if ($encryptedData === false) {
            return false;
        }

        return base64_encode($iv.$tag.$encryptedData);
    }

    public static function decrypt($encryptedData)
    {
        $encryptedData = base64_decode($encryptedData);
        $ivlen = self::getIvLen();

        $iv = substr($encryptedData, 0, $ivlen);
        $tag = substr($encryptedData, $ivlen, self::TAG_LENGTH);
        $encryptedData = substr($encryptedData, $ivlen + self::TAG_LENGTH);

        return openssl_decrypt($encryptedData, self::CIPHER, env('ENCRYPTION_KEY'), $options = OPENSSL_RAW_DATA, $iv, $tag);
    }

    private static function getIvLen(): int
    {
        return openssl_cipher_iv_length(self::CIPHER);
    }
}

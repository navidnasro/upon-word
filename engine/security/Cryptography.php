<?php

namespace engine\security;

use engine\enums\Constants;
use Exception;

defined('ABSPATH') || exit;

class Cryptography
{
    private const ENCRYPTION_METHOD = 'AES-256-CBC';
    private string $key;
    private string $randomKey;
    private string $hmacKey;

    public function __construct(bool $isEncryption)
    {
        if ($isEncryption)
        {
            $this->randomKey = $this->generateRandomKey();
            $this->key = $this->generateKey($this->randomKey);
            $this->hmacKey = $this->generateHmacKey();
        }
    }

    /**
     * Returns a random string to be used as key
     *
     * @param int $length
     * @return string
     */
    public static function generateRandomKey(int $length = 32): string
    {
        try {
            $key = random_bytes($length);
            return bin2hex($key);
        }
        catch (Exception $exception)
        {
            error_log('An unexpected error occurred: '.$exception->getMessage());
            return '1a84ec668a482452b302d815780b692159197b80183bb666532e377f8af3fb5c';
        }
    }

    /**
     * Generates key to make sure of its equality in both encryption and decryption
     *
     * @param string $randomPart
     * @return string
     */
    private function generateKey(string $randomPart): string
    {
        return hash('sha256',Constants::TextDomain.'::'.$randomPart);
    }

    /**
     * Generates key to make sure of its equality in both encryption and decryption
     *
     * @return string
     */
    private function generateHmacKey(): string
    {
        return hash('sha256',$this->key.'_hmac');
    }

    /**
     * Encrypts the data
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $ivLength = openssl_cipher_iv_length(self::ENCRYPTION_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $cipher = openssl_encrypt(
            $data,
            self::ENCRYPTION_METHOD,
            $this->key,
            0,
            $iv
        );

        // calculating and appending hmac for data integrity check
        $hmac = hash_hmac('sha256',$cipher,$this->hmacKey);
        // appending random key to be able to recalculate the key in decryption phase
        return base64_encode($cipher.'::'.$iv.'::'.$this->randomKey.'::'.$hmac);
    }

    /**
     * Decrypts the data
     *
     * @param string $data
     * @return bool|int|string
     */
    public function decrypt(string $data): bool|int|string
    {
        $data = base64_decode($data);
        list($cipher,$iv,$randomKey,$hmac) = explode('::', $data,4);

        // recalculating encryption key because it is missed at decryption point
        $this->key = $this->generateKey($randomKey);
        $this->hmacKey = $this->generateHmacKey();

        // calculating and checking hmac for data integrity
        $expectedHmac = hash_hmac('sha256',$cipher,$this->hmacKey);
        if (!hash_equals($hmac,$expectedHmac))
            return -1;

        return openssl_decrypt(
            $cipher,
            self::ENCRYPTION_METHOD,
            $this->key,
            0,
            $iv
        );
    }
}
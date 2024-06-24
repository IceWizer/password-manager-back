<?php

namespace App\Service;

class EncryptionService
{
    private $encryptionKey;

    public function __construct(string $encryptionKey)
    {
        $this->encryptionKey = bin2hex(base64_decode($encryptionKey));
    }

    public function encrypt(string $data, string $dynamicSalt = ""): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $key = bin2hex(hash('sha256', $this->encryptionKey . $dynamicSalt, true));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $data, string $dynamicSalt = ""): string
    {
        $data = base64_decode($data);
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
        $key = bin2hex(hash('sha256', $this->encryptionKey . $dynamicSalt, true));
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }
}

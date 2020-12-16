<?php

namespace ABCoursework;

/**
 * LibSodiumWrapper: Class for encrypting and decryption data using the LibSodium library.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class LibSodiumWrapper
{
    /**
     * @var string LibSodium encryption/decryption private key.
     */
    private string $key;

    /**
     * @var Base64Wrapper Base64Wrapper instance to encode/decode encrypted data.
     */
    private Base64Wrapper $base64Wrapper;

    /**
     * Creates a new LibSodiumWrapper instance using the give private key and base64wrapper.
     * @param string $key LibSodium private key for encryption/decryption.
     * @param Base64Wrapper $base64Wrapper Base64Wrapper for encoding/decoding encrypted data.
     * @throws \Exception If the private key is an incorrect length.
     */
    public function __construct(string $key, Base64Wrapper $base64Wrapper)
    {
        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new \Exception('Encryption Key is not the correct size! (32 bytes)');
        }
        $this->key = $key;
        $this->base64Wrapper = $base64Wrapper;
    }

    /**
     * Overwrites the key's value in the buffer with zeros for security.
     * @throws \SodiumException
     */
    public function __destruct()
    {
        sodium_memzero($this->key);
    }

    /**
     * Encrypts a given string and encodes it into base64.
     * @param string $unencryptedString The unencrypted string for encryption.
     * @return string|false Encrypted & encoded string or false for an empty input or encryption failure.
     * @throws \SodiumException
     */
    public function encrypt(string $unencryptedString)
    {
        $encryptedString = false;

        if (!empty($unencryptedString)) {
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $encryptedData = sodium_crypto_secretbox($unencryptedString, $nonce, $this->key);
            $encryptedString = $this->base64Wrapper->encode($nonce . $encryptedData);
        }

        sodium_memzero($unencryptedString);

        return $encryptedString;
    }

    /**
     * Decodes a given string from base64 and decrypts it.
     * @param string $encryptedString The encrypted string for decryption.
     * @return string|false Decoded & decrypted string or false for an empty input or decode/decryption failure.
     * @throws \SodiumException
     */
    public function decrypt(string $encryptedString)
    {
        $decryptedString = false;

        if (!empty($encryptedString)) {
            $decodedString = $this->base64Wrapper->decode($encryptedString);

            if ($decodedString !== false) {
                $nonce = mb_substr($decodedString, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
                $ciphertext = mb_substr($decodedString, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
                $decryptedString = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->key);
                sodium_memzero($ciphertext);
            }
        }

        return $decryptedString;
    }

}
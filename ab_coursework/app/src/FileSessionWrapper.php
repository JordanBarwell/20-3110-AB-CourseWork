<?php

namespace ABCoursework;

/**
 * FileSessionWrapper: A wrapper for session file CRUD function, including encryption/decryption for security.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class FileSessionWrapper implements SessionWrapperInterface
{
    /**
     * @var LibSodiumWrapper LibSodiumWrapper used for encrypting/decrypting session values.
     */
    private LibSodiumWrapper $wrapper;

    /**
     * Creates a FileSessionWrapper instance, using a given LibSodiumWrapper for encryption/decryption.
     * @param LibSodiumWrapper $wrapper
     */
    public function __construct(LibSodiumWrapper $wrapper)
    {
        $this->wrapper = $wrapper;
    }

    /**
     * @inheritDoc
     */
    public function check(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        $sessionValue = false;

        if ($this->check($key) !== false) {
            $sessionValue = $this->wrapper->decrypt($_SESSION[$key]);
        }

        return $sessionValue;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value): bool
    {
        $valueSet = false;

        $encryptedValue = $this->wrapper->encrypt($value);
        if ($encryptedValue !== false) {
            $_SESSION[$key] = $encryptedValue;
            $valueSet = ($_SESSION[$key] === $encryptedValue);
        }

        return $valueSet;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): bool
    {
        unset($_SESSION[$key]);
        return !$this->check($key);
    }
}
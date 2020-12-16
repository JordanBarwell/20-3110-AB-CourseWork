<?php

namespace ABCoursework;

/**
 * BcryptWrapper: Class for hashing/verifying passwords using BCrypt.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class BcryptWrapper
{
    /**
     * @var array BCrypt Algorithm hashing options i.e. ['cost' => 12].
     */
    private array $options;

    /**
     * Creates a new BcryptWrapper instance with the given Bcrypt options.
     * @param array $options Bcrypt algorithm options.
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Sets Bcrypt algorithm options to the given array.
     * @param array $options New Bcrypt algorithm options.
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Hashes a given password string using the Bcrypt algorithm.
     * @param string $password The password string needing to be hashed.
     * @return string|false The hash of the password or false if empty string given or password_hash fails.
     */
    public function hash(string $password)
    {
        $hash = false;

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT, $this->options);
        }

        return $hash;
    }

    /**
     * Verifies a given password string matches a given stored hash.
     * @param string $password The password to compare to the hash.
     * @param string $storedHash The stored password hash.
     * @return bool Whether the password matches the stored hash.
     */
    public function verify(string $password, string $storedHash): bool
    {
        $verified = false;

        if (!empty($password) && !empty($storedHash)) {
            $verified = password_verify($password, $storedHash);
        }

        return $verified;
    }

}
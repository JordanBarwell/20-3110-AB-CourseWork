<?php

namespace ABCoursework;

/**
 * SessionWrapperInterface: Defines session value CRUD functions, for manipulating $_SESSION and adding in extra
 * implementations such as encrypting/encoding.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
interface SessionWrapperInterface
{

    /**
     * Checks if a session value has been set with the given identifying key.
     * @param string $key The session value's key.
     * @return bool Whether a value with that key exists in storage.
     */
    public function check(string $key): bool;

    /**
     * Returns a session value from storage, using its identifying key.
     * @param string $key The session value's key.
     * @return false|mixed Either the session value or false if the value doesn't exist or couldn't be retrieved.
     */
    public function get(string $key);

    /**
     * Sets a session value in storage, identified by the key provided.
     * @param string $key The session value's identifying key.
     * @param mixed $value The value needing to be stored.
     * @return bool Whether the value has been successfully set.
     */
    public function set(string $key, $value): bool;

    /**
     * Unsets a session value from storage, using its identifying key.
     * @param string $key The session value's key.
     * @return bool Whether the value has been successfully unset.
     */
    public function remove(string $key): bool;

}
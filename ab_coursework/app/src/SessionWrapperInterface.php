<?php

namespace ABCoursework;

/**
 * SessionWrapperInterface: Interface for session functions.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
interface SessionWrapperInterface
{
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

    /**
     * Returns a session value from storage, using its identifying key.
     * @param string $key The session value's key.
     * @return false|mixed Either the session value or false if the value doesn't exist.
     */
    public function get(string $key);

    /**
     * Checks if a session value has been set with the given identifying key.
     * @param string $key The session value's key.
     * @return bool Whether the value exists in storage.
     */
    public function check(string $key): bool;

    /**
     * Set Wrapper's Logger to a new logger instance.
     * @param \Psr\Log\LoggerInterface $logger Logger to be used in the wrapper.
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger);
}
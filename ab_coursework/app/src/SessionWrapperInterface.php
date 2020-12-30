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

    /**
     * Retrieves the session's CSRF token hashed with the form template filename for inserting into the template.
     * @param string $formTemplateName The template name for the form.
     * @return string The CSRF Token to be inserted into the form.
     */
    public function getCsrfToken(string $formTemplateName): string;

    /**
     * Verifies that the POST method CSRF token matches the session CSRF token hashed with the form template's filename.
     * @param string $postFormToken The POST method CSRF token.
     * @param string $formTemplateName The template name of the form the token was retrieved at.
     * @return bool Whether the CSRF Token matches.
     */
    public function verifyCsrfToken(string $postFormToken, string $formTemplateName): bool;

}
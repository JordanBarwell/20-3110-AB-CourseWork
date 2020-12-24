<?php

namespace ABCoursework;

/**
 * SessionManagerInterface: Defines methods for controlling a session, starting it, regenerating an ID used when
 * permission levels change and destroying a session upon logging out.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
interface SessionManagerInterface
{

    /**
     * Starts a session, using the current session wrapper implementation, should contain session validation and
     * user verification.
     * @param SessionWrapperInterface $wrapper Session Wrapper to get, set, remove and check for variables.
     */
    public static function start(SessionWrapperInterface $wrapper);

    /**
     * Regenerates a session's id and CSRF token. Sets the current session to be invalid, should implement a way of
     * session id management in case of a bad network connection for session validation in start().
     * @param SessionWrapperInterface $wrapper Session Wrapper to get, set, remove and check for variables.
     */
    public static function regenerate(SessionWrapperInterface $wrapper);

    /**
     * Destroys a session, removing all variables and storage of it, used primarily for logging out.
     * @param SessionWrapperInterface $wrapper Session Wrapper to get, set, remove and check for variables.
     */
    public static function destroy(SessionWrapperInterface $wrapper);

}
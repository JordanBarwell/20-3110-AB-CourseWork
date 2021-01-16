<?php

namespace ABCoursework;

/**
 * SessionManager: Contains a start and regenerate session function, to start sessions, checking they're still valid and
 * the request isn't trying to hijack the session. As well as regenerating sessions.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class SessionManager implements SessionManagerInterface
{

    /**
     * @inheritDoc
     */
    public static function start(SessionWrapperInterface $wrapper)
    {
        $https = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : false;
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => 'localhost',
            'secure' => $https,
            'httponly' => true,
            'samesite' => 'strict'
        ]);

        session_start();

        // Validate the session and verify the user.
        if (!(self::validateSession($wrapper) && self::verifyUser($wrapper))) {
            $wrapper->remove('username');
            $wrapper->remove('csrfToken');
        }

        // If no CSRF token set, set it!
        if (!$wrapper->check('csrfToken')) {
            $wrapper->set('csrfToken', bin2hex(random_bytes(32)));
        }

        // If no User Agent or IP Address stored to verify user, store it!
        if (!($wrapper->check('userAgent') && $wrapper->check('ipAddress'))) {
            $wrapper->set('userAgent', $_SERVER['HTTP_USER_AGENT']);
            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
            $wrapper->set('ipAddress', $ip);
        }

    }

    /**
     * @inheritDoc
     */
    public static function regenerate(SessionWrapperInterface $wrapper)
    {
        // Create a new session ID & CSRF token & set the current session to be expired before closing the session.
        $newSessionId = session_create_id();
        $wrapper->set('csrfToken', bin2hex(random_bytes(32)));
        $data = $_SESSION; // Keep the current session's data for the new session.
        $wrapper->set('newSessionId', $newSessionId);
        $wrapper->set('sessionExpired', time());
        session_write_close();

        // Set the session ID to be the new session id and starts the session, transferring data from the old session
        session_id($newSessionId);
        session_start();
        $_SESSION = $data;
        $data = null;
    }

    /**
     * @inheritDoc
     */
    public static function destroy(SessionWrapperInterface $wrapper)
    {
        session_unset();
        session_destroy();
        session_start();
    }

    /**
     * Validates a session, checking if it is expired and if it is, whether it has a new session id.
     * @param SessionWrapperInterface $wrapper Session Wrapper to get, set, remove and check for variables.
     * @return bool Whether the current request is for a valid, recently expired session that has a new ID.
     */
    private static function validateSession(SessionWrapperInterface $wrapper): bool
    {
        $validated = true;
        // Check if Session has expired, if it hasn't then check
        if ($wrapper->check('sessionExpired')) {
            if ($wrapper->get('sessionExpired') < (time() - 60) ) {
                // Could be a hijacking attempt or an unstable network, as session expired a minute before request.
                $validated = false;
            } else if ($wrapper->check('newSessionId')) {
                // Session not fully expired, could be lost cookie or unstable network set session id to the new one and start session again.
                session_write_close();
                session_id($wrapper->get('newSessionId'));
                session_start();
            }
        }
        return $validated;
    }

    /**
     * Verifies the user is the same user that made the previous request using their user agent and IP, however IP
     * checking does have the issue of TOR users and VPN users being logged out.
     * @param SessionWrapperInterface $wrapper Session Wrapper to get, set, remove and check for variables.
     * @return bool Whether the user is the same user as the previous request.
     */
    private static function verifyUser(SessionWrapperInterface $wrapper): bool
    {
        // Check user agent and ip address match stored session values, if not set or not a match, log the user out.
        $agentCheck = $wrapper->check('userAgent') && $wrapper->get('userAgent') === $_SERVER['HTTP_USER_AGENT'];
        $userIp = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $ipCheck = $wrapper->check('ipAddress') && $wrapper->get('ipAddress') === $userIp;
        return $agentCheck && $ipCheck;
    }

}
<?php

namespace ABCoursework;

use Psr\Log\LoggerInterface;

/**
 * UserModel: Class for getting information from the Database related to Users and logging in/logging out the User.
 * @package ABCoursework
 */
class UserModel
{
    /**
     * @var LoggerInterface Logger used to log user actions such as logging in, registering etc.
     */
    private LoggerInterface $logger;

    /**
     * @var DatabaseWrapper Wrapper for database connections, for execution and fetching.
     */
    private DatabaseWrapper $dbWrapper;

    /**
     * @var SqlQueries SqlQueries contain Doctrine QueryBuilder queries for use with DatabaseWrapper.
     */
    private SqlQueries $queries;

    /**
     * @var array Errors generated by methods in this model.
     */
    private array $errors;

    /**
     * Creates a new UserModel instance with the specified Logger, DatabaseWrapper and SqlQueries.
     * @param LoggerInterface $logger Logger used to log user actions such as logging in, registering etc.
     * @param DatabaseWrapper $dbWrapper Wrapper for database connections, for execution and fetching.
     * @param SqlQueries $queries SqlQueries contain Doctrine QueryBuilder queries for use with DatabaseWrapper.
     */
    public function __construct(LoggerInterface $logger, DatabaseWrapper $dbWrapper, SqlQueries $queries)
    {
        $this->logger = $logger;
        $this->dbWrapper = $dbWrapper;
        $this->queries = $queries;
        $this->errors = [];
    }

    /**
     * Returns all errors from this model.
     * @return array Errors generated by methods in this model.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Checks whether or not a user with the entered username, email or phoneNumber is already registered.
     * @param array $params Cleaned Parameters to query the database to check if the details exist.
     * @return bool Whether or not the user details already exist.
     */
    public function checkUserExists(array $params)
    {
        $result = false;

        $username = $params['cleanedSiteUsername'];
        $email = $params['cleanedUserEmail'];
        $phoneNumber = $params['cleanedPhoneNumber'];

        $query = $this->queries->checkUserDetails($username, $email, $phoneNumber);
        if ($query) {
            $this->logger->info('Database User Exists Check', ['userData' => $params]);
            $queryResult = $this->dbWrapper->executeAndFetch($query);
            if ($queryResult) {
                $result = true;

                if ($queryResult['username'] === $username) {
                    $this->errors['SiteUsername'] = 'Username Already Exists!';
                }
                if ($queryResult['email'] === $email) {
                    $this->errors['UserEmail'] = 'Email Already In Use!';
                }
                if ((int)$queryResult['phone'] === $phoneNumber) {
                    $this->errors['PhoneNumber'] = 'Phone Number Already In Use!';
                }
            }
        } else {
            $this->errors['Database'] = 'Database connection couldn\'t be established, please try again later!';
        }

        return $result;
    }

    /**
     * Registers a user, inserting their details into the database,
     * setting their username in the session and regenerating the session id.
     * @param array $params Cleaned Parameters for insertion into the database.
     * @param SessionWrapperInterface $wrapper Session Wrapper used to set session username and for session regeneration.
     * @param SessionManagerInterface $manager Session Manager used to regenerate session on successful registration.
     * @return bool Whether user registration was successful.
     */
    public function registerUser(array $params, SessionWrapperInterface $wrapper, SessionManagerInterface $manager)
    {
        $result = false;

        $username = $params['cleanedSiteUsername'];
        $passwordHash = $params['cleanedSitePassword'];
        $email = $params['cleanedUserEmail'];
        $phoneNumber = $params['cleanedPhoneNumber'];

        $query = $this->queries->insertUser($username, $passwordHash, $email, $phoneNumber);

        if ($query && $this->dbWrapper->execute($query)) {
            $result = true;
            $wrapper->set('username', $username);
            $manager::regenerate($wrapper);
            $this->logger->info('New User Registered', ['username' => $username]);
        } else {
            $this->errors['Database'] = 'Database connection couldn\'t be established, please try again later!';
        }

        return $result;
    }

    /**
     * Gets login details for the given username if they exist, otherwise returns an empty array.
     * @param string $username Username to query the database with for login details.
     * @return array Login details for the user if they exist, else an empty array.
     */
    public function getLoginDetails(string $username)
    {
        $result = [];

        $query = $this->queries->getUserLoginData($username);

        if ($query)
        {
            $queryResult = $this->dbWrapper->executeAndFetch($query);
            $result = $queryResult ?? [];
            $this->logger->info('Login Details Requested', ['username' => $username]);
        } else {
            $this->errors['Database'] = 'Database connection couldn\'t be established, please try again later!';
        }

        return $result;
    }

    /**
     * Logs in a user, setting their username in the session and regenerating the session id.
     * @param string $username User's username, to set it in the session.
     * @param SessionWrapperInterface $wrapper Session Wrapper used to set username in the session and regenerate session id.
     * @param SessionManagerInterface $manager Session Manager used to regenerate session id using the wrapper.
     * @return bool Whether or not the login was successful.
     */
    public function loginUser(string $username, SessionWrapperInterface $wrapper, SessionManagerInterface $manager)
    {
        $result = $wrapper->set('username', $username);
        if ($result) {
            $manager::regenerate($wrapper);
            $this->logger->info('User Logged In', ['username' => $username]);
        }
        return $result;
    }

    /**
     * Logs out a user, destroying their session.
     * @param SessionWrapperInterface $wrapper Session Wrapper used by manager to destroy the session.
     * @param SessionManagerInterface $manager Session Manager used to destroy the session.
     */
    public function logoutUser(SessionWrapperInterface $wrapper, SessionManagerInterface $manager)
    {
        $this->logger->info('User Logged Out', ['username' => $wrapper->get('username')]);
        $manager::destroy($wrapper);
    }

}
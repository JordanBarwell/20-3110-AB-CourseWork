<?php

namespace ABCoursework;

use Doctrine\DBAL\Query\QueryBuilder;
use Psr\Log\LoggerInterface;

/**
 * SqlQueries: Class for building and executing doctrine SQL queries.
 *
 * @package ABCoursework
 * @author Team AB
 */
class SqlQueries
{
    /**
     * @var QueryBuilder|null Doctrine query builder with connection to database or null on connection failure.
     */
    private ?QueryBuilder $queryBuilder;

    /**
     * Creates a new instance of SqlQueries with the given QueryBuilder.
     * @param QueryBuilder|null $queryBuilder Query Builder for building SQL queries or null if DB connection failed.
     */
    public function __construct(?QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Builds a query to retrieve user information from the database that matches user input to check a user isn't already
     * registered.
     * @param string $username Cleaned input username to check if it already exists in the database.
     * @param string $email Cleaned input email address to check if it already exists in the database.
     * @param string $phoneNumber Cleaned input phone number to check if it already exists in the database.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function checkUserDetails(string $username, string $email, string $phoneNumber)
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->select('username', 'email', 'phone')
                ->from('users')
                ->where('username = :username ')
                ->orWhere('email = :email')
                ->orWhere('phone = :phone')
                ->setParameters([
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phoneNumber
                ]);
        }

        return $result;
    }

    /**
     * Builds a query to insert a new user into the database.
     * @param string $username Username of new user to be inserted.
     * @param string $passwordHash Password hash of the new user to be inserted.
     * @param string $email Email address of the new user to be inserted.
     * @param int $phoneNumber Phone number of the new user to be inserted.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function insertUser(string $username, string $passwordHash, string $email, int $phoneNumber)
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->insert('users')
                ->values([
                    'username' => ':username',
                    'password' => ':password',
                    'email' => ':email',
                    'phone' => ':phone'
                ])->setParameters([
                    'username' => $username,
                    'password' => $passwordHash,
                    'email' => $email,
                    'phone' => $phoneNumber
                ]);
        }

        return $result;
    }

    /**
     * Builds a query to retrieve existing user login data from the database.
     * @param string $username Username used in the query to identify user details.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function getUserLoginData(string $username)
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->select('username', 'password')
                ->from('users')
                ->where('username = :username')
                ->setParameter('username', $username);
        }

        return $result;
    }

    /**
     * Builds a query to retrieve a user's phone number from the database.
     * @param string $username Username of the user whose phoone number is required.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function getUserPhoneNumber(string $username)
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->select('phone')
                ->from('users')
                ->where('username = :username')
                ->setParameter('username', $username);
        }

        return $result;
    }

    /**
     * Builds a query to insert a message into the database.
     * @param array $parameters Cleaned message details to be inserted into the database.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function insertMessage(array $parameters)
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->insert('messages')
                ->values([
                    'source' => ':source',
                    'received' => ':received',
                    'bearer' => ':bearer',
                    'ref' => ':ref',
                    'temperature' => ':temperature',
                    'fan' => ':fan',
                    'keypad' => ':keypad',
                    'switchOne' => ':switchOne',
                    'switchTwo' => ':switchTwo',
                    'switchThree' => ':switchThree',
                    'switchFour' => ':switchFour'
                ])->setParameters([
                    'source' => $parameters['source'],
                    'received' => $parameters['received'],
                    'bearer' => $parameters['bearer'],
                    'ref' => $parameters['ref'],
                    'temperature' => $parameters['temperature'],
                    'fan' => $parameters['fan'],
                    'keypad' => $parameters['keypad'],
                    'switchOne' => $parameters['switchOne'],
                    'switchTwo' => $parameters['switchTwo'],
                    'switchThree' => $parameters['switchThree'],
                    'switchFour' => $parameters['switchFour']
                ]);
        }

        return $result;
    }

    /**
     * Builds a query to retrieve a number of messages from the database.
     * @param int $numberOfMessages Number of messages to be retrieved.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function getMessages(int $numberOfMessages)
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->select(
                'source',
                'received',
                'bearer',
                'ref',
                'temperature',
                'fan',
                'keypad',
                'switchOne',
                'switchTwo',
                'switchThree',
                'switchFour'
            )->from('messages')
                ->orderBy('received', 'DESC')
                ->setMaxResults($numberOfMessages);
        }

        return $result;
    }

    /**
     * Builds a query to retrieve the latest received datetime from the database.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function getLatestMessageDateTime()
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->select('received')
                ->from('messages')
                ->orderBy('received', 'DESC')
                ->setMaxResults(1);
        }

        return $result;
    }

    /**
     * Builds a query to retrieve all messages from the database.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function getAllMessages()
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->select(
                'source',
                'received',
                'bearer',
                'ref',
                'temperature',
                'fan',
                'keypad',
                'switchOne',
                'switchTwo',
                'switchThree',
                'switchFour'
            )->from('messages')
                ->orderBy('received', 'DESC');
        }

        return $result;
    }

    /**
     * Builds a query to retrieve all users from the database.
     * @return QueryBuilder|null Built Query or null on database connection failure.
     */
    public function getAllUsers()
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->select(
                'id',
                'username',
                'email',
                'phone'
            )->from('users')
                ->orderBy('id');
        }

        return $result;
    }

}

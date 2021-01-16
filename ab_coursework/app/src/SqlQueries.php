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

    public function insertUser(string $username, string $passwordHash, string $email, int $phoneNumber)
    {
        $result = null;

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

        return $result;
    }

    public function getUserLoginData(string $username)
    {
        $result = null;

        if ($this->queryBuilder !== null) {
            $result = $this->queryBuilder->select('id', 'username', 'password')
                ->from('users')
                ->where('username = :username')
                ->setParameter('username', $username);
        }

        return $result;
    }

    public function insertMessage($parameters)
    {
        $queryBuilder = $this->queryBuilder->insert('messages')
            ->values([
                'source' => ':source',
                'destination' => ':destination',
                'received' => ':received',
                'bearer' => ':bearer',
                'ref' => ':ref',
                'message' => ':message'
            ])->setParameters([
                ':source' => $parameters['source'],
                ':destination' => $parameters['destination'],
                ':received' => $parameters['received'],
                ':bearer' => $parameters['bearer'],
                ':ref' => $parameters['ref'],
                ':message' => $parameters['message']
            ]);

        $storeResult = $queryBuilder->execute();

        return $storeResult;
    }

    public function viewMessages(int $numberOfMsg)
    {
        $result = '';

        $queryBuilder = $this->queryBuilder->select('source',
            'destination', 'received', 'bearer', 'ref', 'message')
            ->from('messages')
            ->orderBy('received', 'DESC')
            ->setMaxResults($numberOfMsg);

        $result = $queryBuilder->execute()->fetchAllAssociative();
        return $result;
    }

}

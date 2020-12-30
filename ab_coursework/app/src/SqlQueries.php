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
     * @var LoggerInterface Logger used to log database queries.
     */
    private LoggerInterface $logger;

    /**
     * @var QueryBuilder Doctrine query builder with connection to database.
     */
    private QueryBuilder $queryBuilder;

    /**
     * Creates a new instance of SqlQueries with the given QueryBuilder and Logger.
     * @param QueryBuilder $queryBuilder Query Builder for building SQL queries.
     * @param LoggerInterface $logger Logger for logging queries.
     */
    public function __construct(LoggerInterface $logger, QueryBuilder $queryBuilder)
    {
        $this->logger = $logger;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Sets new QueryBuilder for all future queries of this instance.
     * @param QueryBuilder $queryBuilder New QueryBuilder.
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Sets Logger to the given logger.
     * @param LoggerInterface $logger New Logger.
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function storeUserData(array $cleanedParameters, string $hashedPassword)
    {
        $queryBuilder = $this->queryBuilder->insert('users')
            ->values([
                'username' => ':username',
                'password' => ':password',
                'email' => ':email',
                'phone' => ':phone'
            ])->setParameters([
                ':username' => $cleanedParameters['cleanedSiteUsername'],
                ':password' => $hashedPassword,
                ':email' => $cleanedParameters['cleanedUserEmail'],
                ':phone' => ($cleanedParameters['cleanedPhoneNumber'])
            ]);

        $storeResult = $queryBuilder->execute();

        if ($storeResult) {
            $userId = $queryBuilder->getConnection()->lastInsertId();
        }

        return $userId;
    }

    public function checkUserDetailsExist($parameters)
    {
        $result = '';
        $username = $parameters['cleanedSiteUsername'];
        $email = $parameters['cleanedUserEmail'];
        $phoneNumber = $parameters['cleanedPhoneNumber'];

        $queryBuilder = $this->queryBuilder->select('username', 'email', 'phone')
            ->from('users')
            ->where(
                'username = :username ')
            ->orWhere('email = :email')
            ->orWhere('phone = :phone')
            ->setParameters([
                ':username' => $username,
                ':email' => $email,
                ':phone' => $phoneNumber
            ]);

        $result = $queryBuilder->execute()->fetchAssociative();
        return $result;

    }

}

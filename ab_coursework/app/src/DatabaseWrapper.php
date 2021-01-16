<?php

namespace ABCoursework;

use Doctrine\DBAL\{Connection, DriverManager, Exception};
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Query\QueryBuilder;
use Psr\Log\LoggerInterface;

/**
 * DatabaseWrapper: A wrapper for connecting to, executing and fetching with/from the database.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class DatabaseWrapper
{
    /**
     * @var LoggerInterface Logger for logging any errors or activities performed on the database.
     */
    private LoggerInterface $logger;

    /**
     * @var Connection|null Doctrine DBAL Connection used to executing queries, null if connection failed.
     */
    private ?Connection $connection;

    /**
     * Creates a DatabaseWrapper instance with a logger to log queries and connection settings for DB connection.
     * @param LoggerInterface $logger
     * @param array $connectionSettings
     */
    public function __construct(LoggerInterface $logger, array $connectionSettings)
    {
        $this->logger = $logger;
        $this->connect($connectionSettings);
    }

    /**
     * Remove Database Connection on object destruction.
     */
    public function __destruct()
    {
        $this->connection = null;
    }

    /**
     * Attempts to create a connection to the database using the provided settings.
     * @param array $connectionSettings Settings for the database connection including login, server info etc.
     */
    public function connect(array $connectionSettings)
    {
        $this->connection = null;

        try {
            $this->connection = DriverManager::getConnection($connectionSettings);
            $this->logger->info('Database Connection Established', $connectionSettings);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->logger->alert('Database Connection Failed: ' . $message, $connectionSettings);
        }
    }

    /**
     * Retrieve a QueryBuilder from the current database connection.
     * @return QueryBuilder|null QueryBuilder for SqlQueries or false on connection failure.
     */
    public function getQueryBuilder()
    {
        return $this->connection->createQueryBuilder() ?? null;
    }

    /**
     * Executes a DBAL QueryBuilder Query.
     * @param QueryBuilder|null $queryBuilder QueryBuilder with Query for execution or null on connection failure.
     * @return ResultStatement|int|null ResultStatement for fetching result, number of affected rows or null on failure.
     */
    public function execute(?QueryBuilder $queryBuilder)
    {
        $result = null;

        if ($queryBuilder !== null) {
            try {
                $result = $queryBuilder->execute();
                $this->logger->info('Database Query Executed', ['queryBuilder' => $queryBuilder]);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->logger->alert('Database Query Failed: ' . $message, ['queryBuilder' => $queryBuilder]);
            }
        }

        return $result;
    }

    /**
     * Retrieve the last inserted ID from the database connection.
     * @return string|null String representation of last ID or null on connection failure.
     */
    public function getLastInsertedId()
    {
        $result = null;

        if ($this->connection !== null) {
            $result = $this->connection->lastInsertId();
            $this->logger->info('Database Last Inserted ID retrieved');
        }

        return $result;
    }

    /**
     * Fetches one row of query output from the DB and escapes it for displaying, to prevent stored XSS.
     * @param ResultStatement|null $statement Result to retrieve data from or null on query/connection error.
     * @return array|null Escaped output array or null on fetch/query/connection error.
     */
    public function fetch(?ResultStatement $statement)
    {
        $result = null;

        if ($statement !== null) {
            try {
                $data = $statement->fetchAssociative();
                if ($data !== false) {
                    $result = $this->escapeOutput($data, false);
                }
                $this->logger->info('Database Data Fetched', ['resultStatement' => $statement]);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->logger->alert('Database Fetch Failed: ' . $message, ['resultStatement' => $statement]);
            }
        }

        return $result;
    }

    /**
     * Fetches all rows of query output from the DB and escapes it for displaying, to prevent stored XSS.
     * @param ResultStatement|null $statement Result to retrieve data from or null on query/connection error.
     * @return array|null Escaped output array or null on fetch/query/connection error.
     */
    public function fetchAll(?ResultStatement $statement)
    {
        $result = null;

        if ($statement !== null) {
            try {
                $data = $statement->fetchAllAssociative();
                if ($data !== false) {
                    $result = $this->escapeOutput($data);
                }
                $this->logger->info('Database Data Fetched', ['resultStatement' => $statement]);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $this->logger->alert('Database Fetch Failed: ' . $message, ['resultStatement' => $statement]);
            }
        }

        return $result;
    }

    /**
     * Executes and fetches one row of escaped output from the DB, preventing stored XSS.
     * @param QueryBuilder|null $queryBuilder QueryBuilder with Query for execution or null on connection failure.
     * @return array|null Escaped output array or null on fetch/query/connection error.
     */
    public function executeAndFetch(?QueryBuilder $queryBuilder)
    {
        $executionResult = $this->execute($queryBuilder);
        return $this->fetch($executionResult);
    }

    /**
     * Executes and fetches all rows of escaped output from the DB, preventing stored XSS.
     * @param QueryBuilder|null $queryBuilder QueryBuilder with Query for execution or null on connection failure.
     * @return array|null Escaped output array or null on fetch/query/connection error.
     */
    public function executeAndFetchAll(?QueryBuilder $queryBuilder)
    {
        $executionResult = $this->execute($queryBuilder);
        return $this->fetch($executionResult);
    }

    /**
     * Escapes DB output for displaying, to prevent stored XSS.
     * @param array $rowSet Unescaped, dangerous DB output.
     * @return array Escaped DB Output.
     */
    private function escapeOutput(array $rowSet, bool $fetchAll = true): array
    {
        $result = [];

        if ($fetchAll) {
            foreach ($rowSet as $rowNum => $rowData) {
                foreach ($rowData as $column => $value) {
                    $result[$rowNum][$column] = htmlspecialchars($value, ENT_COMPAT | ENT_HTML5);
                }
            }
        } else {
            foreach ($rowSet as $column => $value) {
                $result[$column] = htmlspecialchars($value, ENT_COMPAT | ENT_HTML5);
            }
        }

        return $result;
    }

}
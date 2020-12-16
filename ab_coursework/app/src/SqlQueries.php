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
     * @var QueryBuilder Doctrine query builder with connection to database.
     */
    private QueryBuilder $queryBuilder;

    /**
     * @var LoggerInterface Logger used to log database queries.
     */
    private LoggerInterface $logger;

    /**
     * Creates a new instance of SqlQueries with the given QueryBuilder and Logger.
     * @param QueryBuilder $queryBuilder Query Builder for building SQL queries.
     * @param LoggerInterface $logger Logger for logging queries.
     */
    public function __construct(QueryBuilder $queryBuilder, LoggerInterface $logger)
    {
        $this->queryBuilder = $queryBuilder;
        $this->logger = $logger;
    }

    /**
     * Sets new QueryBuilder for all future queries of this instace.
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



}

<?php
/**
 * dependencies.php
 *
 * Script to set all container dependencies, classes, views, loggers, queries etc.
 *
 * Author: Team AB
 * Date: 28/11/2020
 *
 */

$container['view'] = function ($container) {
  $view = new Slim\Views\Twig(
    $container['settings']['view']['templatePath'],
    $container['settings']['view']['twig']
  );

  // Add Twig Extension
  $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
  $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
  $view->addExtension(new Twig\Extension\DebugExtension());

  return $view;
};

$container[\Psr\Log\LoggerInterface::class] = function ($container) {
    $logger = new Monolog\Logger('logger');
    $logFile = '../../logs/ABCoursework.log';

    // Create Log Formatting
    $fileHandler = new Monolog\Handler\StreamHandler($logFile, \Monolog\Logger::DEBUG);
    $dateFormat = 'd/M/Y H:i:s';
    $output = "%datetime% | %level_name% | %message% | %context% | %extra%\n";
    $formatter = new Monolog\Formatter\LineFormatter($output, $dateFormat, false, true);
    $fileHandler->setFormatter($formatter);

    $logger->pushHandler($fileHandler);

    return $logger;
};

$container['SoapWrapper'] = function ($container) {
    return new ABCoursework\SoapWrapper($container[\Psr\Log\LoggerInterface::class], $container['settings']['soap']['connection']);
};

$container['Base64Wrapper'] = function ($container) {
    return new ABCoursework\Base64Wrapper();
};

$container['BcryptWrapper'] = function ($container) {
    return new ABCoursework\BcryptWrapper($container['settings']['bcrypt']);
};

$container['LibSodiumWrapper'] = function ($container) {
    return new ABCoursework\LibSodiumWrapper($container['settings']['naKey'], $container['Base64Wrapper']);
};

$container['XmlParser'] = function ($container) {
    return new ABCoursework\XmlParser();
};

$container[\ABCoursework\SessionWrapperInterface::class] = function ($container) {
    return new \ABCoursework\FileSessionWrapper($container['LibSodiumWrapper']);
};

$container[\ABCoursework\SessionManagerInterface::class] = function ($container) {
    return new \ABCoursework\SessionManager();
};

$container['Validator'] = function ($container) {
    return new ABCoursework\Validator();
};

$container['QueryBuilder'] = function ($container) {
    $connection = \Doctrine\DBAL\DriverManager::getConnection($container['settings']['doctrine']);
    return $connection->createQueryBuilder();
};

$container['SqlQueries'] = function ($container) {
    return new ABCoursework\SqlQueries($container[\Psr\Log\LoggerInterface::class], $container['QueryBuilder']);
};

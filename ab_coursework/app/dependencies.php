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

use ABCoursework\Base64Wrapper;
use ABCoursework\BcryptWrapper;
use ABCoursework\DatabaseWrapper;
use ABCoursework\FileSessionWrapper;
use ABCoursework\LibSodiumWrapper;
use ABCoursework\SessionManager;
use ABCoursework\SessionManagerInterface;
use ABCoursework\SessionWrapperInterface;
use ABCoursework\SoapWrapper;
use ABCoursework\SqlQueries;
use ABCoursework\UserModel;
use ABCoursework\Validator;
use ABCoursework\XmlParser;
use Psr\Log\LoggerInterface;

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

$container[LoggerInterface::class] = function ($container) {
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
    return new SoapWrapper($container[LoggerInterface::class], $container['settings']['soap']['connection']);
};

$container['Base64Wrapper'] = function ($container) {
    return new Base64Wrapper();
};

$container['BcryptWrapper'] = function ($container) {
    return new BcryptWrapper($container['settings']['bcrypt']);
};

$container['LibSodiumWrapper'] = function ($container) {
    return new LibSodiumWrapper($container['settings']['naKey'], $container['Base64Wrapper']);
};

$container['XmlParser'] = function ($container) {
    return new XmlParser();
};

$container[SessionWrapperInterface::class] = function ($container) {
    return new FileSessionWrapper($container['LibSodiumWrapper']);
};

$container[SessionManagerInterface::class] = function ($container) {
    return new SessionManager();
};

$container['Validator'] = function ($container) {
    return new Validator();
};

$container['DatabaseWrapper'] = function ($container) {
    return new DatabaseWrapper($container[LoggerInterface::class], $container['settings']['doctrine']);
};

$container['SqlQueries'] = function ($container) {
    return new SqlQueries($container['DatabaseWrapper']->getQueryBuilder());
};

$container['UserModel'] = function ($container) {
    return new UserModel($container[LoggerInterface::class], $container['DatabaseWrapper'], $container['SqlQueries']);
};
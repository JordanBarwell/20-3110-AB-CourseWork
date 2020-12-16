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
  $view = new \Slim\Views\Twig(
    $container['settings']['view']['templatePath'],
    $container['settings']['view']['twig']
  );

  // Instantiate and add Slim specific extension
  $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
  $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
  $view->addExtension(new Twig\Extension\DebugExtension());

  return $view;
};

$container['loggerWrapper'] = function ($container) {
    $loggingWrapper = new Monolog\Logger('logger');
    $logsFilePath = '../../../logs/';
    $logsFileName = 'abCoursework.log';
    $logFile = $logsFilePath . $logsFileName;
    $loggingWrapper->pushHandler(new \Monolog\Handler\StreamHandler($logFile, \Monolog\Logger::DEBUG));

    return $loggingWrapper;
};

$container['sqlQueries'] = function ($container) {
  return new \ABCoursework\SQLQueries();
};

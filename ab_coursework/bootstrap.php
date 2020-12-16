<?php
/**
 * bootstrap.php
 *
 * Script to create/configure the application, the dependency container and the application routes.
 *
 * Author: Team AB
 * Date: 28/11/2020
 *
 */

require 'vendor/autoload.php';

$settings = require __DIR__ . '/app/settings.php';

$makeTrace = false;

if ($makeTrace && function_exists('xdebug_start_trace'))
{
  xdebug_start_trace();
}

$container = new \Slim\Container($settings);

require __DIR__ . '/app/dependencies.php';

$app = new \Slim\App($container);

require __DIR__ . '/app/routes.php';

$app->run();

if ($makeTrace && function_exists('xdebug_stop_trace'))
{
  xdebug_stop_trace();
}

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
use \Slim\{Container, App};

require 'vendor/autoload.php';

$settings = require __DIR__ . '/app/settings.php';

$makeTrace = true;

if ($makeTrace && function_exists('xdebug_start_trace'))
{
  xdebug_start_trace();
}

$container = new Container($settings);

require __DIR__ . '/app/dependencies.php';

$app = new App($container);

require __DIR__ . '/app/middleware.php';
require __DIR__ . '/app/routes.php';

$app->run();

if ($makeTrace && function_exists('xdebug_stop_trace'))
{
  xdebug_stop_trace();
}

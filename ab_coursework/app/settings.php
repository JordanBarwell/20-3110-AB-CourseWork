<?php
/**
 * settings.php
 *
 * Script to configure all application settings, some ini values and constant definitions.
 *
 * Author: Team AB
 * Date: 28/11/2020
 */

ini_set('display_errors', 'On');
ini_set('html_errors', 'On');
ini_set('xdebug.trace_output_name', 'ab_coursework.%t');
ini_set('xdebug.trace_format', 1);

define('DIRSEP', DIRECTORY_SEPARATOR);

$urlRoot = $_SERVER['SCRIPT_NAME'];
$urlRoot = implode('/', explode('/', $urlRoot, -1));
$cssPath = $urlRoot . '/css/style.css';

$scriptFilename = $_SERVER["SCRIPT_FILENAME"];
$explodedScriptFilename = explode('/' , $scriptFilename, '-1');
$scriptPath = implode('/', $explodedScriptFilename) . '/';

define('CSS_PATH', $cssPath);
define('APP_NAME', 'AB Coursework');
define('LANDING_PAGE', $urlRoot);

$settings = [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => true,
        'determineRouteBeforeAppMiddleware' => true,
        'debug' => true,
        'classPath' => __DIR__ . '/src/',
        'naKey' => 'Jared & Jordan & Charlie Web App',
        'view' => [
            'templatePath' => __DIR__ . '/templates/',
            'twig' => [
                'cache' => false,
                'auto_reload' => true,
                'debug' => true
            ]
        ],
        'doctrine' => [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'abcoursework_db',
            'port' => '3306',
            'user' => 'abcoursework_user',
            'password' => 'abcoursework_pass',
            'charset' => 'utf8mb4',
            'driverOptions' => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => true
            ]
        ],
        'soap' => [
            'connection' => [
                'wsdl' => 'https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl',
                'options' => [
                    'trace' => true,
                    'exceptions' => true
                ]
            ],
            'login' => [
                'username' => '',
                'password' => ''
            ]
        ],
        'bcrypt' => [
            'cost' => 14
        ]
    ]
];

return $settings;

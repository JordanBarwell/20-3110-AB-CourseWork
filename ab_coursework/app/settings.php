<?php
/**
 * settings.php
 *
 * Script to configure all application settings, some ini values and constant definitions.
 *
 * Author: Team AB
 * Date: 28/11/2020
 *
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

//define ('LIB_CHART_OUTPUT_PATH', 'media/charts/');
//define ('LIB_CHART_FILE_PATH', $scriptPath);
//define ('LIB_CHART_CLASS_PATH', 'libchart/classes/');

$wsdl = 'https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl';
define('WSDL', $wsdl);

$settings = [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'debug' => true,
        'classPath' => __DIR__ . '/src/',
        'view' => [
            'templatePath' => __DIR__ . '/templates/',
            'twig' => [
                'cache' => false,
                'auto_reload' => true,
                'debug' => true
            ]],
        'pdo_settings' => [
            'rdbms' => 'mysql',
            'host' => 'localhost',
            'dbName' => 'abcoursework_db',
            'port' => '3306',
            'userName' => 'abcoursework_user',
            'userPassword' => 'abcoursework_pass',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true
            ]
        ]
    ]
];

return $settings;

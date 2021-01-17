<?php

use ABCoursework\SoapWrapper;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * SoapWrapperTest: Soap Wrapper Tests.
 *
 * @author Team AB (Jared)
 */
class SoapWrapperTest extends TestCase
{
    /**
     * @var Psr\Log\LoggerInterface Logger to be used for testing.
     */
    static LoggerInterface $logger;

    /**
     * @var array Real SOAP connection settings for testing.
     */
    static array $soapSettings;

    /**
     * @var array Fake SOAP connection settings for testing.
     */
    static array $fakeSettings;

    /**
     * Runs before all tests, initialising all necessary dependencies and retrieving settings values if needed.
     */
    public static function setUpBeforeClass(): void
    {
        // Logger setup
        self::$logger = new Logger('logger');
        $logFile = '../../../logs/ABCourseworkTest.log';

        // Create Log Formatting and Handler
        $fileHandler = new Monolog\Handler\StreamHandler($logFile, Logger::DEBUG);
        $dateFormat = 'd/m/Y H:i:s';
        $output = "%datetime% | %level_name% | %message% | %context% | %extra%\n";
        $formatter = new Monolog\Formatter\LineFormatter($output, $dateFormat, false, true);
        $fileHandler->setFormatter($formatter);
        self::$logger->pushHandler($fileHandler);

        //Grab Needed Settings from settings.php
        self::$soapSettings = (@require 'settings.php')['settings']['soap'];
        self::$fakeSettings = [
            'wsdl' => 'not a wsdl link :)',
            'options' => []
        ];
    }

    /**
     * Test for correct instantiation, SoapWrapper client should be an instance of SoapClient.
     */
    public function testCorrectCreation()
    {
        $wrapper = new SoapWrapper(self::$logger, (self::$soapSettings)['connection']);
        $this->assertTrue($wrapper->getClient() instanceof SoapClient);
        return $wrapper;
    }

    /**
     * Test for incorrect instantiation, SoapWrapper client should be an instance of null i.e. not SoapClient.
     * @depends testCorrectCreation
     */
    public function testIncorrectCreation()
    {
        $this->assertFalse((new SoapWrapper(self::$logger, self::$fakeSettings))->getClient() instanceof SoapClient);
    }

    /**
     * Test for newSoapConnection using verified connection info, will return true if new connection has been established.
     * @depends testCorrectCreation
     * @param SoapWrapper $wrapper Wrapper to perform testing with.
     */
    public function testNewConnection(SoapWrapper $wrapper)
    {
        $this->assertTrue($wrapper->newSoapConnection(self::$soapSettings['connection']));
    }

    /**
     * Test for newSoapConnection using incorrect connection info, will return false as new connection couldn't be established.
     * @depends testCorrectCreation
     * @depends testNewConnection
     * @param SoapWrapper $wrapper Wrapper to perform testing with.
     */
    public function testIncorrectNewConnection(SoapWrapper $wrapper)
    {
        $this->assertFalse($wrapper->newSoapConnection(self::$fakeSettings));
    }

    /**
     * Test for performSoapFunction, using peekMessages.
     * @depends testCorrectCreation
     * @param SoapWrapper $wrapper Wrapper to perform testing with.
     */
    public function testSoapFunctionCall(SoapWrapper $wrapper)
    {
        $userData = self::$soapSettings['login'];
        $params = [
            'username' => $userData['username'],
            'password' => $userData['password'],
            'count' => 1,
            'deviceMsisdn' => '',
            'countryCode' => '44'
        ];
        $testResult = $wrapper->performSoapFunction('Testing', 'peekMessages', $params);
        $this->assertNotNull($testResult);
    }

    /**
     * Test for performSoapFunction, using peekMessages.
     * @depends testCorrectCreation
     * @depends testSoapFunctionCall
     * @param SoapWrapper $wrapper Wrapper to perform testing with.
     */
    public function testIncorrectSoapFunctionCall(SoapWrapper $wrapper)
    {
        $testResult = $wrapper->performSoapFunction('Testing', 'peekMessages', []);
        $this->assertNull($testResult);
    }

}

<?php

use ABCoursework\Base64Wrapper;
use ABCoursework\FileSessionWrapper;
use ABCoursework\LibSodiumWrapper;
use PHPUnit\Framework\TestCase;

/**
 * FileSessionWrapperTest: File Session Wrapper Tests.
 *
 * @author Team AB (Jared)
 */
class FileSessionWrapperTest extends TestCase
{
    /**
     * @var string Test Key used for the get and set functions.
     */
    private static string $testKey;

    /**
     * @var string Test Value used for the get and set functions
     */
    private static string $testValue;

    /**
     * @var FileSessionWrapper FileSessionWrapper used for the tests.
     */
    private static FileSessionWrapper $wrapper;

    /**
     * Runs before all tests, initialising all necessary dependencies and retrieving settings values if needed.
     */
    public static function setUpBeforeClass(): void
    {
        // Set test key and value for session testing.
        self::$testKey = 'Testing';
        self::$testValue = 'Testing Testing 123';
        $naWrapper = new LibSodiumWrapper((@require 'settings.php')['settings']['naKey'], new Base64Wrapper());
        self::$wrapper = new FileSessionWrapper($naWrapper);
    }

    /**
     * Runs after the tests have finished running, emptying the $_SESSION array in case it's used in other tests.
     */
    public static function tearDownAfterClass(): void
    {
        $_SESSION = [];
    }

    // NO TEST FOR CHECK/REMOVE AS THEY'RE JUST USING INBUILT ISSET/UNSET FUNCTIONS

    /**
     * Tests that the set function returns true when a non-empty value is entered.
     */
    public function testSet()
    {
        $this->assertTrue(self::$wrapper->set(self::$testKey, self::$testValue));
    }

    /**
     * Tests that the set function returns false when an empty value is entered.
     * @depends testSet
     */
    public function testSetEmptyValue()
    {
        $this->assertFalse(self::$wrapper->set(self::$testKey, ''));
    }

    /**
     * Tests that the get function returns the same value set using the set function above.
     * @depends testSet
     */
    public function testGet()
    {
        $this->assertEquals(self::$testValue, self::$wrapper->get(self::$testKey));
    }

}

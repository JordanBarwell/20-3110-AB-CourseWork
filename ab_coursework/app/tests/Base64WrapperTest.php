<?php

use ABCoursework\Base64Wrapper;
use PHPUnit\Framework\TestCase;

/**
 * Base64WrapperTest: Base64Wrapper Tests.
 *
 * @author Team AB (Jared)
 */
class Base64WrapperTest extends TestCase
{
    /**
     * @var string Test data to be encoded.
     */
    static string $testData;

    /**
     * @var string Test data encoded with inbuilt function for comparison to wrapper result.
     */
    static string $testDataEncoded;

    /**
     * @var Base64Wrapper Base64Wrapper to test.
     */
    static Base64Wrapper $wrapper;

    /**
     * Runs before all tests, initialising all necessary dependencies and retrieving settings values if needed.
     */
    public static function setUpBeforeClass(): void
    {
        // Set test data and expected encode result variables.
        self::$testData = 'Testing Testing 123';
        self::$testDataEncoded = base64_encode(self::$testData);
        self::$wrapper = new Base64Wrapper();
    }

    /**
     * Test encoding a string returns string base64 encoded.
     * @return false|string The result of encoding test data.
     */
    public function testEncode()
    {
        $encodedData = self::$wrapper->encode(self::$testData);
        $this->assertEquals(self::$testDataEncoded, $encodedData);
        return $encodedData;
    }

    /**
     * Test encoding an empty string to see if it returns false.
     * @depends testEncode
     */
    public function testEmptyEncode()
    {
        $encodedData = self::$wrapper->encode('');
        $this->assertFalse($encodedData);
    }

    /**
     * Test decoding a base64 encoded string returns the original data.
     * @depends testEncode
     * @param string $encodedData Base64 encoded data to be decoded.
     */
    public function testDecode(string $encodedData)
    {
        $decodedData = self::$wrapper->decode($encodedData);
        $this->assertEquals(self::$testData, $decodedData);
    }

    /**
     * Test decoding an empty string to see if it returns false.
     * @depends testDecode
     */
    public function testEmptyDecode()
    {
        $decodedData = self::$wrapper->decode('');
        $this->assertFalse($decodedData);
    }

    /**
     * Test decoding a string with characters out of base64 character range to see if it returns false.
     * @depends testDecode
     */
    public function testOutOfRangeDecode()
    {
        $decodedData = self::$wrapper->decode('???');
        $this->assertFalse($decodedData);
    }

}

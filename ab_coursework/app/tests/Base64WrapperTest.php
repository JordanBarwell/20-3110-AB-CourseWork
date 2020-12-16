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
     * Runs before all tests, initialising all necessary dependencies and retrieving settings values if needed.
     */
    public static function setUpBeforeClass(): void
    {
        // Set test data and expected encode result variables.
        self::$testData = 'Testing Testing 123';
        self::$testDataEncoded = base64_encode(self::$testData);
    }

    /**
     * Test encoding a string returns string base64 encoded.
     * @return false|string The result of encoding test data.
     */
    public function testEncode()
    {
        $encodedData = (new Base64Wrapper())->encode(self::$testData);
        $this->assertEquals(self::$testDataEncoded, $encodedData);
        return $encodedData;
    }

    /**
     * Test encoding an empty string to see if it returns false.
     * @depends testEncode
     */
    public function testEmptyEncode()
    {
        $encodedData = (new Base64Wrapper())->encode('');
        $this->assertFalse($encodedData);
    }

    /**
     * Test decoding a base64 encoded string returns the original data.
     * @depends testEncode
     */
    public function testDecode($encodedData)
    {
        $decodedData = (new Base64Wrapper())->decode($encodedData);
        $this->assertEquals(self::$testData, $decodedData);
    }

    /**
     * Test decoding an empty string to see if it returns false.
     * @depends testDecode
     */
    public function testEmptyDecode()
    {
        $decodedData = (new Base64Wrapper())->decode('');
        $this->assertFalse($decodedData);
    }

    /**
     * Test decoding a string with characters out of base64 character range to see if it returns false.
     * @depends testDecode
     */
    public function testOutOfRangeDecode()
    {
        $decodedData = (new Base64Wrapper())->decode('???');
        $this->assertFalse($decodedData);
    }

}

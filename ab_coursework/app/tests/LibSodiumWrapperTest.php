<?php

use ABCoursework\LibSodiumWrapper;
use PHPUnit\Framework\TestCase;

/**
 * LibSodiumWrapperTest: LibSodium Wrapper Tests.
 *
 * @author Team AB (Jared)
 */
class LibSodiumWrapperTest extends TestCase
{
    /**
     * @var string Web App's encryption key.
     */
    static string $key;

    /**
     * @var string Test data to be encrypted/decrypted.
     */
    static string $testData;

    /**
     * Runs before all tests, initialising all necessary dependencies and retrieving settings values if needed.
     */
    public static function setUpBeforeClass(): void
    {
        self::$key = (@require 'settings.php')['settings']['naKey'];
        self::$testData = 'Testing Testing 123';
    }

    /**
     * Runs after the class' tests have finished, overwriting the key data so it can't be retrieved from memory.
     */
    public static function tearDownAfterClass(): void
    {
        sodium_memzero(self::$key);
    }

    /**
     * Tests the constructor works correctly with the correct key length and a Base64Wrapper.
     * @return LibSodiumWrapper Wrapper to be used in all tests.
     * @throws Exception If key is too short.
     */
    public function testConstructor()
    {
        $wrapper = new LibSodiumWrapper(self::$key, new \ABCoursework\Base64Wrapper());
        $this->assertInstanceOf(LibSodiumWrapper::class, $wrapper);
        return $wrapper;
    }

    /**
     * Tests that an exception is thrown if the key is too short in the constructor.
     * @throws Exception If key is too short.
     * @depends testConstructor
     */
    public function testConstructorException()
    {
        $this->expectException(Exception::class);
        $badWrapper = new LibSodiumWrapper('Key too short!', new \ABCoursework\Base64Wrapper());
    }

    /**
     * Tests that the length of the wrapper encrypted string is the same as the library encrypted string.
     * @depends testConstructor
     */
    public function testEncryption(LibSodiumWrapper $wrapper)
    {
        $wrapperEncrypt = $wrapper->encrypt(self::$testData);
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $libraryEncrypt = base64_encode($nonce. sodium_crypto_secretbox(
            self::$testData,
            $nonce,
            self::$key
        ));

        $this->assertEquals(strlen($wrapperEncrypt), strlen($libraryEncrypt));
    }

    /**
     * Tests that the encryption function returns false if an empty value is entered.
     * @depends testConstructor
     * @depends testEncryption
     */
    public function testEncryptionEmptyString(LibSodiumWrapper $wrapper)
    {
        $this->assertFalse($wrapper->encrypt(''));
    }

    /**
     * Tests that the decryption function returns the original value when an encrypted value is passed to it.
     * @depends testConstructor
     * @depends testEncryption
     */
    public function testDecryption(LibSodiumWrapper $wrapper)
    {
        $wrapperDecrypt = $wrapper->decrypt($wrapper->encrypt(self::$testData));
        $this->assertEquals($wrapperDecrypt, self::$testData);
    }

    /**
     * Tests that the decryption function returns false if an empty value is entered.
     * @depends testConstructor
     * @depends testDecryption
     */
    public function testDecryptionEmptyString(LibSodiumWrapper $wrapper)
    {
        $this->assertFalse($wrapper->decrypt(''));
    }




}

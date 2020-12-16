<?php

use ABCoursework\LibSodiumWrapper;
use PHPUnit\Framework\TestCase;

class LibSodiumWrapperTest extends TestCase
{
    static string $key;
    static string $testData;

    public static function setUpBeforeClass(): void
    {
        self::$key = (@require 'settings.php')['settings']['naKey'];
        self::$testData = 'Testing Testing 123';
    }

    public static function tearDownAfterClass(): void
    {
        sodium_memzero(self::$key);
    }

    public function testConstructor()
    {
        $wrapper = new LibSodiumWrapper(self::$key, new \ABCoursework\Base64Wrapper());
        $this->assertInstanceOf(LibSodiumWrapper::class, $wrapper);
        return $wrapper;
    }

    /**
     *
     * @throws Exception
     * @depends testConstructor
     */
    public function testConstructorException()
    {
        $this->expectException(Exception::class);
        $badWrapper = new LibSodiumWrapper('Key too short!', new \ABCoursework\Base64Wrapper());
    }

    /**
     *
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
     *
     * @depends testConstructor
     * @depends testEncryption
     */
    public function testEncryptionEmptyString(LibSodiumWrapper $wrapper)
    {
        $this->assertFalse($wrapper->encrypt(''));
    }

    /**
     *
     * @depends testConstructor
     * @depends testEncryption
     */
    public function testDecryption(LibSodiumWrapper $wrapper)
    {
        $wrapperDecrypt = $wrapper->decrypt($wrapper->encrypt(self::$testData));
        $this->assertEquals($wrapperDecrypt, self::$testData);
    }

    /**
     *
     * @depends testConstructor
     * @depends testDecryption
     */
    public function testDecryptionEmptyString(LibSodiumWrapper $wrapper)
    {
        $this->assertFalse($wrapper->decrypt(''));
    }




}

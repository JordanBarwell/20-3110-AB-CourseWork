<?php

use ABCoursework\BcryptWrapper;
use PHPUnit\Framework\TestCase;

/**
 * BcryptWrapperTest: BCrypt Wrapper Tests.
 *
 * @author Team AB (Jared)
 */
class BcryptWrapperTest extends TestCase
{

    /**
     * @var string Password to be hashed and compared to expected hash.
     */
    static string $testPassword;

    /**
     * @var string|false|null Password hash result used for verify.
     */
    static $testPasswordHash;

    /**
     * @var BcryptWrapper BcryptWrapper to test.
     */
    static BcryptWrapper $wrapper;

    /**
     * Runs before all tests, initialising all necessary dependencies and retrieving settings values if needed.
     */
    public static function setUpBeforeClass(): void
    {
        $options = (require 'settings.php')['settings']['bcrypt'];
        self::$testPassword = 'Testing Testing 123';
        self::$testPasswordHash = password_hash(self::$testPassword, PASSWORD_BCRYPT, $options);
        self::$wrapper = new BcryptWrapper($options);
    }

    /**
     * Test hash function returns correct hash of password.
     * @return BcryptWrapper
     */
    public function testHash()
    {
        $wrapper = self::$wrapper;
        $hash = $wrapper->hash(self::$testPassword);
        $this->assertTrue(password_verify(self::$testPassword, $hash));
        return $wrapper;
    }

    /**
     * Test hash function returns false if empty password string given.
     * @depends testHash
     * @param BcryptWrapper $wrapper Wrapper to perform testing with.
     */
    public function testHashEmpty(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->hash(''));
    }

    /**
     * Test verification function returns true with known matching password and hash.
     * @depends testHash
     * @param BcryptWrapper $wrapper Wrapper to perform testing with.
     */
    public function testVerify(BcryptWrapper $wrapper)
    {
        $this->assertTrue($wrapper->verify(self::$testPassword, self::$testPasswordHash));
    }

    /**
     * Test verification function returns false with known non-matching password and hash.
     * @depends testHash
     * @depends testVerify
     * @param BcryptWrapper $wrapper Wrapper to perform testing with.
     */
    public function testVerifyWrongPassword(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->verify('Not the password!', self::$testPasswordHash));
    }

    /**
     * Test verification function returns false when given empty password.
     * @depends testHash
     * @depends testVerify
     * @param BcryptWrapper $wrapper Wrapper to perform testing with.
     */
    public function testVerifyEmptyPassword(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->verify('', self::$testPasswordHash));
    }

    /**
     * Test verification function returns false when given empty hash.
     * @depends testHash
     * @depends testVerify
     * @param BcryptWrapper $wrapper Wrapper to perform testing with.
     */
    public function testVerifyEmptyHash(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->verify(self::$testPassword, ''));
    }

    /**
     * Test verification function returns false when given empty password and hash.
     * @depends testHash
     * @depends testVerify
     * @param BcryptWrapper $wrapper Wrapper to perform testing with.
     */
    public function testVerifyBothEmpty(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->verify('', ''));
    }

}

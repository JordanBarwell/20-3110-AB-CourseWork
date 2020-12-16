<?php

use ABCoursework\BcryptWrapper;
use PHPUnit\Framework\TestCase;

/**
 * BcryptWrapperTest: BCrypt Wrapper Tests.
 *
 */
class BcryptWrapperTest extends TestCase
{
    /**
     * @var array BCrypt algorithm options.
     */
    static array $options;

    /**
     * @var string Password to be hashed and compared to expected hash.
     */
    static string $testPassword;

    /**
     * @var string|false|null Password hash result used for verify.
     */
    static $testPasswordHash;

    /**
     * Runs before all tests, initialising all necessary dependencies and retrieving settings values if needed.
     */
    public static function setUpBeforeClass(): void
    {
        self::$options = (require 'settings.php')['settings']['bcrypt'];
        self::$testPassword = 'Testing Testing 123';
        self::$testPasswordHash = password_hash(self::$testPassword, PASSWORD_BCRYPT, self::$options);
    }

    /**
     * Test hash function returns correct hash of password.
     * @return BcryptWrapper
     */
    public function testHash()
    {
        $wrapper = new BcryptWrapper(self::$options);
        $hash = $wrapper->hash(self::$testPassword);
        $this->assertTrue(password_verify(self::$testPassword, $hash));
        return $wrapper;
    }

    /**
     * Test hash function returns false if empty password string given.
     * @depends testHash
     */
    public function testHashEmpty(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->hash(''));
    }

    /**
     * Test verification function returns true with known matching password and hash.
     * @depends testHash
     */
    public function testVerify(BcryptWrapper $wrapper)
    {
        $this->assertTrue($wrapper->verify(self::$testPassword, self::$testPasswordHash));
    }

    /**
     * Test verification function returns false with known non-matching password and hash.
     * @depends testHash
     * @depends testVerify
     */
    public function testVerifyWrongPassword(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->verify('Not the password!', self::$testPasswordHash));
    }

    /**
     * Test verification function returns false when given empty password.
     * @depends testHash
     * @depends testVerify
     */
    public function testVerifyEmptyPassword(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->verify('', self::$testPasswordHash));
    }

    /**
     * Test verification function returns false when given empty hash.
     * @depends testHash
     * @depends testVerify
     */
    public function testVerifyEmptyHash(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->verify(self::$testPassword, ''));
    }

    /**
     * Test verification function returns false when given empty password and hash.
     * @depends testHash
     * @depends testVerify
     */
    public function testVerifyBothEmpty(BcryptWrapper $wrapper)
    {
        $this->assertFalse($wrapper->verify('', ''));
    }

}

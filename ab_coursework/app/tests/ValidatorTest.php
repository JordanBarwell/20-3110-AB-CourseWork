<?php

use ABCoursework\Validator;
use PHPUnit\Framework\TestCase;

/**
 * ValidatorTest: Validator Tests.
 *
 * @author Team AB (Jared)
 */
class ValidatorTest extends TestCase
{

    /**
     * Tests string validation doesn't return false with a clean string.
     */
    public function testValidateStringCorrect()
    {
        $validator = new ABCoursework\Validator();
        $value = 'ABCD';
        $this->assertNotFalse($validator->validateString('test', $value));
    }

    /**
     * Tests string validation returns false and says the field is required with an empty input.
     * @depends testValidateStringCorrect
     */
    public function testValidateStringEmpty()
    {
        $validator = new ABCoursework\Validator();
        $value = '';
        $this->assertFalse($validator->validateString('test', $value));
        $this->assertEquals('This field is required!', $validator->getError('test'));
    }

    /**
     * Tests string validation returns false and says the string must be between the min and max limit when too short
     * an input is entered.
     * @depends testValidateStringCorrect
     */
    public function testValidateStringLessThanMinLength()
    {
        $validator = new ABCoursework\Validator();
        $value = 'ABCD';
        $this->assertFalse($validator->validateString('test', $value, 5, 10));
        $this->assertEquals('Must be between 5 and 10 characters!', $validator->getError('test'));
    }

    /**
     * Tests string validation returns false and says the string must be between the min and max limit when too long
     * an input is entered.
     * @depends testValidateStringCorrect
     */
    public function testValidateStringGreaterThanMaxLength()
    {
        $validator = new ABCoursework\Validator();
        $value = 'ABCD';
        $this->assertFalse($validator->validateString('test', $value, 1, 3));
        $this->assertEquals('Must not be longer than 3 characters!', $validator->getError('test'));
    }

    /**
     * Tests int validation doesn't return false with a clean int.
     */
    public function testValidateIntCorrect()
    {
        $validator = new ABCoursework\Validator();
        $value = '1';
        $this->assertNotFalse($validator->validateInt('test', $value));
    }

    /**
     * Tests int validation returns false and says that the input must be a valid integer if a random string is entered.
     * @depends testValidateIntCorrect
     */
    public function testValidateIntString()
    {
        $validator = new ABCoursework\Validator();
        $value = 'ABCD';
        $this->assertFalse($validator->validateInt('test', $value));
        $this->assertEquals('Must be a valid integer!', $validator->getError('test'));
    }

    /**
     * Tests int validation returns false and says the field is required if an empty value is entered.
     * @depends testValidateIntCorrect
     */
    public function testValidateIntEmpty()
    {
        $validator = new ABCoursework\Validator();
        $value = '';
        $this->assertFalse($validator->validateInt('test', $value));
        $this->assertEquals('This field is required!', $validator->getError('test'));
    }

    /**
     * Tests int validation returns false and says the int must be between the min and max values if too long of a numeric
     * string is entered.
     * @depends testValidateIntCorrect
     */
    public function testValidateIntTooLong()
    {
        $validator = new ABCoursework\Validator();
        $value = '111111111111111111111111111111111';
        $this->assertFalse($validator->validateInt('test', $value));
        $this->assertEquals('Must be between '.PHP_INT_MIN.' and '.PHP_INT_MAX.'! (Inclusive)', $validator->getError('test'));
    }

    /**
     * Tests int validation returns false and says the int string must be between the min and max limit when an input
     * is less than the minimum value.
     * @depends testValidateIntCorrect
     */
    public function testValidateIntLessThanMin()
    {
        $validator = new ABCoursework\Validator();
        $value = '25';
        $this->assertFalse($validator->validateInt('test', $value, 30));
        $this->assertEquals('Must be between 30 and '.PHP_INT_MAX.'! (Inclusive)', $validator->getError('test'));
    }

    /**
     * Tests int validation returns false and says the int string must be between the min and max limit when an input
     * is greater than the maximum value.
     * @depends testValidateIntCorrect
     */
    public function testValidateIntGreaterThanMax()
    {
        $validator = new ABCoursework\Validator();
        $value = '25';
        $this->assertFalse($validator->validateInt('test', $value, PHP_INT_MIN, 10));
        $this->assertEquals('Must be between '.PHP_INT_MIN.' and 10! (Inclusive)', $validator->getError('test'));
    }

    /**
     * Tests email validation doesn't return false with a valid email address.
     */
    public function testValidateEmailCorrect()
    {
        $validator = new ABCoursework\Validator();
        $value = 'a@a.com';
        $this->assertNotFalse($validator->validateEmail('test', $value));
    }

    /**
     * Tests email validation returns false and says the input must be an email address when random string in entered.
     * @depends testValidateEmailCorrect
     */
    public function testValidateEmailString()
    {
        $validator = new ABCoursework\Validator();
        $value = 'ABCD';
        $this->assertFalse($validator->validateEmail('test', $value));
        $this->assertEquals('Must be a valid e-mail address!', $validator->getError('test'));
    }

    /**
     * Tests email validation returns false and says the field is required if an empty string is entered.
     * @depends testValidateEmailCorrect
     */
    public function testValidateEmailEmpty()
    {
        $validator = new ABCoursework\Validator();
        $value = '';
        $this->assertFalse($validator->validateEmail('test', $value));
        $this->assertEquals('This field is required!', $validator->getError('test'));
    }

    /**
     * Tests email validation returns false and says the input must be an email address when too long of a string is entered.
     * @depends testValidateEmailCorrect
     */
    public function testValidateEmailTooLong()
    {
        $validator = new ABCoursework\Validator();
        $value = str_repeat('a', 301);
        $this->assertFalse($validator->validateEmail('test', $value));
        $this->assertEquals('Must be a valid e-mail address!', $validator->getError('test'));
    }

    /**
     * Tests datetime validation doesn't return false with a valid datetime.
     */
    public function testValidateDateTimeCorrect()
    {
        $validator = new ABCoursework\Validator();
        $value = '01/02/2010 21:22:23';
        $this->assertNotFalse($validator->validateDateTime('test', $value));
    }

    /**
     * Tests datetime validation returns false and says input must be a valid datetime when a random string is entered.
     * @depends testValidateDateTimeCorrect
     */
    public function testValidateDateTimeString()
    {
        $validator = new ABCoursework\Validator();
        $value = 'ABCD';
        $this->assertFalse($validator->validateDateTime('test', $value));
        $this->assertEquals('Must be a valid datetime!', $validator->getError('test'));
    }

    /**
     * Tests datetime validation returns false and says the field is required when empty string is entered.
     * @depends testValidateDateTimeCorrect
     */
    public function testValidateDateTimeEmpty()
    {
        $validator = new ABCoursework\Validator();
        $value = '';
        $this->assertFalse($validator->validateDateTime('test', $value));
        $this->assertEquals('This field is required!', $validator->getError('test'));
    }

    /**
     * Tests datetime validation returns false and says the input must be a valid datetime when too long a string is entered.
     * @depends testValidateDateTimeCorrect
     */
    public function testValidateDateTimeTooLong()
    {
        $validator = new ABCoursework\Validator();
        $value = str_repeat('a', 101);
        $this->assertFalse($validator->validateDateTime('test', $value));
        $this->assertEquals('Must be a valid datetime!', $validator->getError('test'));
    }

    /**
     * Tests phone number validation doesn't return false when a correct phone number is entered.
     */
    public function testValidatePhoneNumberCorrect()
    {
        $validator = new ABCoursework\Validator();
        $value = '+447478954721';
        $this->assertNotFalse($validator->validatePhoneNumber('test', $value));
    }

    /**
     * Tests phone number validation returns false and says input must be a valid phone number when a random string is entered.
     * @depends testValidatePhoneNumberCorrect
     */
    public function testValidatePhoneNumberString()
    {
        $validator = new ABCoursework\Validator();
        $value = 'ABCD';
        $this->assertFalse($validator->validatePhoneNumber('test', $value));
        $this->assertEquals('Must be a valid phone number!', $validator->getError('test'));
    }

    /**
     * Tests phone number validation returns false and says input is required if an empty string is entered.
     * @depends testValidatePhoneNumberCorrect
     */
    public function testValidatePhoneNumberEmpty()
    {
        $validator = new ABCoursework\Validator();
        $value = '';
        $this->assertFalse($validator->validatePhoneNumber('test', $value));
        $this->assertEquals('This field is required!', $validator->getError('test'));
    }

    /**
     * Tests phone number validation returns false and says input must be a valid phone number is too long a number is entered.
     * @depends testValidatePhoneNumberCorrect
     */
    public function testValidatePhoneNumberTooLong()
    {
        $validator = new ABCoursework\Validator();
        $value = '+44444444444444444444444444';
        $this->assertFalse($validator->validatePhoneNumber('test', $value));
        $this->assertEquals('Must be a valid phone number!', $validator->getError('test'));
    }

}
<?php

namespace ABCoursework;

/**
 * Validator: Validates strings, ints, emails, date times and phone numbers, as well as creating error messages.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class Validator
{
    /**
     * @var array An associative array of errors, keys are the same as they keys given for all validation functions.
     */
    private array $errors = [];

    /**
     * Returns error for the given key.
     * @param string $key Key given to validation function.
     * @return string|null The error message for the given key or null if there isn't an error.
     */
    public function getError(string $key)
    {
        return $this->errors[$key] ?? null;
    }

    /**
     * Returns all errors from the given Validator instance.
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Checks if a validated value is valid.
     * @param string $key Key given to validation function to check if errors are present.
     * @return bool Whether the given key was valid after validation was performed.
     */
    public function isValid(string $key): bool
    {
        return !isset($this->errors[$key]);
    }

    /**
     * Checks if all validated inputs are valid.
     * @return bool Whether all validated inputs are valid.
     */
    public function areAllValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Tests that a string's length is between the min and max lengths supplied (inclusive), after sanitizing it.
     * @param string $key Identifying key of the input value, used to store/retrieve errors.
     * @param string $value String to be checked.
     * @param int $minLength Minimum String Length. (inclusive)
     * @param int $maxLength Maximum String Length. (inclusive)
     * @return false|string Sanitized string or false if invalid.
     */
    public function validateString(string $key, string $value, int $minLength = 1, int $maxLength = 100)
    {
        $result = false;

        if (strlen($value) <= $maxLength) {
            $sanitizedString = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            $sanitizedStringLength = strlen($sanitizedString);

            if (empty($value) && $minLength !== 0) {
                $this->errors[$key] = 'This field is required!';
            } elseif ($sanitizedStringLength < $minLength || $sanitizedStringLength > $maxLength) {
                $this->errors[$key] = "Must be between $minLength and $maxLength characters!";
            } else {
                $result = $sanitizedString;
            }
        } else {
            $this->errors[$key] = "Must not be longer than $maxLength characters!";
        }

        return $result;
    }

    /**
     * Tests that the entered string is numeric, and is an int.
     * @param string $key Identifying key of the input value, used to store/retrieve errors.
     * @param string $value String to be checked if it is numeric and an int once sanitised.
     * @param int $min Minimum integer value. (Inclusive)
     * @param int $max Maximum integer value. (Inclusive)
     * @return false|int Integer or false if invalid.
     */
    public function validateInt(string $key, string $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX)
    {
        $result = false;

        if (!empty($value) && strlen($value) <= 20) { // php ints have a max length of 20, because of the '-' sign.
            $sanitizedInt = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            $validatedInt = filter_var($sanitizedInt, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);

            if ($validatedInt === false) {
                $this->errors[$key] = empty($sanitizedInt) ? 'Must be a valid integer!' : "Must be between $min and $max! (Inclusive)";
            } else {
                $result = $validatedInt;
            }
        } elseif (strlen($value) > 20) {
            $this->errors[$key] = "Must be between $min and $max! (Inclusive)";
        } else {
            $this->errors[$key] = 'This field is required!';
        }

        return $result;
    }

    /**
     * Tests if the entered string is a valid email address.
     * @param string $key Identifying key of the input value, used to store/retrieve errors.
     * @param string $email Email string to be checked.
     * @return bool|string Email address or false if it's invalid.
     */
    public function validateEmail(string $key, string $value)
    {
        $result = false;

        if (!empty($value) && strlen($value) <= 300) {
            $sanitisedEmail = filter_var($value, FILTER_SANITIZE_EMAIL);
            $validatedEmail = filter_var($sanitisedEmail, FILTER_VALIDATE_EMAIL);

            if ($validatedEmail === false) {
                $this->errors[$key] = 'Must be a valid e-mail address!';
            } else {
                $result = $validatedEmail;
            }
        } elseif (strlen($value) > 300) {
            $this->errors[$key] = 'Must be a valid e-mail address!';
        } else {
            $this->errors[$key] = 'This field is required!';
        }

        return $result;
    }

    /**
     * Tests if the supplied string is a valid datetime string in the format specified.
     * @param string $key Identifying key of the input value, used to store/retrieve errors.
     * @param string $value Datetime string to be checked.
     * @param string $format Format the datetime string should match, default value is used by the SOAP server.
     * @return false|string The date-time string or false if it's invalid.
     */
    public function validateDateTime(string $key, string $value, string $format = 'd/m/Y H:i:s')
    {
        $result = false;

        if (!empty($value) && strlen($value) <= 100) {
            $date = \DateTime::createFromFormat($format, $value);
            $valid = \DateTime::getLastErrors();
            if ($date !== false && $valid['warning_count'] == 0 && $valid['error_count'] == 0) {
                $result = $value;
            } else {
                $this->errors[$key] = 'Must be a valid datetime!';
            }
        } elseif (strlen($value) > 100) {
            $this->errors[$key] = 'Must be a valid datetime!';
        } else {
            $this->errors[$key] = 'This field is required!';
        }

        return $result;
    }

    /**
     * Tests a phone number string to see if it fits the international number format with leading '+' and no spaces,
     * it only checks the formatting, not if the number is real.
     * @param string $key Identifying key of the input value, used to store/retrieve errors.
     * @param string $value Phone number string to be checked.
     * @return false|string The phone number or false if it's invalid.
     */
    public function validatePhoneNumber(string $key, string $value)
    {
        $result = false;

        if (!empty($value)) {
            // Reg Exp from https://oreilly.com/library/view/regular-expressions-cookbook/9781449327453/ch04s03.html
            if (preg_match('/^\+(?:[0-9]){6,14}[0-9]$/', $value) === 1) {
                $result = $value;
            } else {
                $this->errors[$key] = 'Must be a valid phone number!';
            }
        } else {
            $this->errors[$key] = 'This field is required!';
        }

        return $result;
    }

}
<?php

namespace ABCoursework;

/**
 * Base64Wrapper: Class for encoding/decoding base64.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class Base64Wrapper
{
    /**
     * Encodes a given string into base64.
     * @param mixed $data Data to be Base64 encoded.
     * @return false|string Encoded data or false if $data was empty.
     */
    public function encode($data)
    {
        $encodedData = false;

        if (!empty($data))
        {
            $encodedData = base64_encode($data);
        }

        return $encodedData;
    }

    /**
     * Decodes a given string from base64.
     * @param mixed $encodedData Base64 encoded data.
     * @return false|string Decoded data or false if $encodedData was empty or has characters outside Base64 range.
     */
    public function decode($encodedData)
    {
        $data = false;

        if (!empty($encodedData))
        {
            $data = base64_decode($encodedData, true);
        }

        return $data;
    }
}
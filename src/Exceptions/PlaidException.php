<?php

namespace AlexVargash\LaravelStripePlaid\Exceptions;

use Exception;

class PlaidException extends Exception
{
    /**
     * Create an exception with the provided message from Plaid.
     *
     * @param  string  $message
     * @return AlexVargash\LaravelStripePlaid\Exceptions\PlaidException
     */
    public static function badRequest($message)
    {
        return new static($message);
    }

    /**
     * Create an exception for missing Plaid keys.
     *
     * @return AlexVargash\LaravelStripePlaid\Exceptions\PlaidException
     */
    public static function missingKeys()
    {
        return new static('{ "display_message": null, "error_code": "MISSING_KEYS", "error_message": "Missing Plaid credentials.", "error_type": "INVALID_INPUT" }');
    }

    /**
     * Create an exception for invalid environment.
     *
     * @return AlexVargash\LaravelStripePlaid\Exceptions\PlaidException
     */
    public static function invalidEnvironment()
    {
        return new static('{ "display_message": null, "error_code": "INVALID_ENVIRONMENT", "error_message": "The environment must be: sandbox, development or production.", "error_type": "INVALID_INPUT" }');
    }
}

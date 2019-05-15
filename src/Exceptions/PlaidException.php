<?php

namespace AlexVargash\LaravelStripePlaid\Exceptions;

use Exception;

class PlaidException extends Exception
{
    public static function badRequest($message)
    {
        return new static($message);
    }

    public static function missingKeys()
    {
        return new static('{ "display_message": null, "error_code": "MISSING_KEYS", "error_message": "Missing Plaid credentials.", "error_type": "INVALID_INPUT" }');
    }

    public static function invalidEnvironment()
    {
        return new static('{ "display_message": null, "error_code": "INVALID_ENVIRONMENT", "error_message": "The environment must be: sandbox or production.", "error_type": "INVALID_INPUT" }');
    }
}

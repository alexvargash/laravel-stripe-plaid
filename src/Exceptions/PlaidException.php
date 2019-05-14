<?php

namespace AlexVargash\LaravelStripePlaid\Exceptions;

use Exception;

class PlaidException extends Exception
{
    public static function badRequest($message)
    {
        return new static($message);
    }

    public static function missingKeys($key)
    {
        return new static("The Plaid '{$key}' key is missing.");
    }

    public static function invalidEnvironment()
    {
        return new static("The Plaid environment must be: 'sandbox' or 'production'.");
    }
}

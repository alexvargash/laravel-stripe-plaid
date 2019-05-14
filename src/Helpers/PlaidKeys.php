<?php

namespace AlexVargash\LaravelStripePlaid\Helpers;

use AlexVargash\LaravelStripePlaid\Exceptions\PlaidException;

class PlaidKeys
{
    const KEYS = ['secret', 'client_id', 'account_id', 'public_token', 'environment'];
    const ENVIRONMENTS = ['sandbox', 'production'];

    public static function validate($plaidKeys)
    {
        self::reviewKeys($plaidKeys);
        self::reviewEnvironment($plaidKeys['environment']);

        return $plaidKeys;
    }

    public static function reviewKeys($plaidKeys)
    {
        foreach (self::KEYS as $key) {
            if (array_key_exists($key, $plaidKeys)) continue;

            throw PlaidException::missingKeys($key);
        }
    }

    public static function reviewEnvironment($environment)
    {
        if (!in_array($environment, self::ENVIRONMENTS)) {
            throw PlaidException::invalidEnvironment();
        }
    }
}

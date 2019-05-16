<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The environment on which the API host will be set up, the accepted values
    | are: sandbox, development and production.
    | https://plaid.com/docs/#api-host
    |
    */
    'environment' => env('PLAID_ENVIRONMENT', ''),

    /*
    |--------------------------------------------------------------------------
    | Secret
    |--------------------------------------------------------------------------
    |
    | Private API key, here you need to add the respective secret key based on
    | the environment that is set up. This value can be found on your Plaid
    | account under the keys section.
    | https://plaid.com/docs/#glossary
    |
    */
    'secret' => env('PLAID_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Client Id
    |--------------------------------------------------------------------------
    |
    | The client id is an identifier for the Plaid account and can be found
    | on your Plaid account under the keys section. This value is always
    | the same, doesn't change based on environment.
    | https://plaid.com/docs/#glossary
    |
    */
    'client_id' => env('PLAID_CLIENT_ID', '')
];

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
    'client_id' => env('PLAID_CLIENT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Client Name
    |--------------------------------------------------------------------------
    |
    | The name of your application, as it should be displayed in Link.
    | https://plaid.com/docs/#glossary
    |
    */
    'client_name' => env('PLAID_CLIENT_NAME', ''),

    /*
    |--------------------------------------------------------------------------
    | Language
    |--------------------------------------------------------------------------
    |
    | The language that Link should be displayed in.
    | When using a Link customization, the language configured here must match the setting 
    | in the customization, or the customization will not be applied.
    | Supported languages are: English ('en'), French ('fr'), Spanish ('es'), Dutch ('nl')
    | https://plaid.com/docs/#glossary
    |
    */
    'language' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Country Codes
    |--------------------------------------------------------------------------
    |
    | Specify an array of Plaid-supported country codes using the ISO-3166-1 alpha-2 country code standard. 
    | Note that if you initialize with a European country code, your users will see the European consent panel 
    | during the Link flow. 
    | If Link is launched with multiple country codes, only products that you are enabled for in all countries will be used by Link.
    | Supported country codes are: US, CA, ES, FR, GB, IE, NL. Example value: ['US', 'CA']. 
    | https://plaid.com/docs/#glossary
    |
    */
    'country_codes' => ['US'],

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    |
    | List of Plaid product(s) you wish to use. If launching Link in update mode, 
    | should be omitted; required otherwise 
    | Supported products are: transactions, auth, identity, assets, investments, liabilities, payment_initiation. 
    | Example value: ['auth', 'transactions'] 
    | https://plaid.com/docs/#glossary
    |
    */
    'products' => ['auth', 'transactions'],
    
];

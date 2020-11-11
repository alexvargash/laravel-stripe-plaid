# Laravel Stripe Plaid

[![Build Status](https://travis-ci.org/alexvargash/laravel-stripe-plaid.svg?branch=master)](https://travis-ci.org/alexvargash/laravel-stripe-plaid)
[![StyleCI](https://github.styleci.io/repos/185878123/shield?branch=master)](https://github.styleci.io/repos/185878123)

Simple package for creating a Stripe Bank Account Token from Plaid Link.

## Installation

This package requires Laravel 5.5 or higher.

Require the package using composer:

```bash
composer require alexvargash/laravel-stripe-plaid
```

The service provider will automatically get registered.

You can publish the configuration file with:

```bash
php artisan vendor:publish --provider="AlexVargash\LaravelStripePlaid\StripePlaidServiceProvider" --tag="config"
```

When published, the `config/stripe-plaid.php` config file contains:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The environment on which the API host will be set up, the accepted values
    | are: sandbox, development and production.
    | https://plaid.com/docs/api/#api-host
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
    | https://plaid.com/docs/api/tokens/#token-endpoints
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
    | https://plaid.com/docs/api/tokens/#token-endpoints
    |
    */
    'client_id' => env('PLAID_CLIENT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Client Name
    |--------------------------------------------------------------------------
    |
    | The name of your application, as it should be displayed in Link.
    | https://plaid.com/docs/api/tokens/#token-endpoints
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
    | https://plaid.com/docs/api/tokens/#token-endpoints
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
    | https://plaid.com/docs/api/tokens/#token-endpoints
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
    | https://plaid.com/docs/api/tokens/#token-endpoints
    |
    */
    'products' => ['auth', 'transactions'],

];
```

## Usage

First, add the Plaid keys and environment to the `config/stripe-plaid.php` file or on your `.env`.

```bash
PLAID_ENVIRONMENT=sandbox
PLAID_SECRET=your_plaid_secret_key
PLAID_CLIENT_ID=your_plaid_client_id
PLAID_CLIENT_NAME=your_app_name
```
Then, you need to create the `link_token` which is required as a parameter when initializing Link. Once Link has been initialized, it returns a `public_token`.

The `createLinkToken` function accept the parameters `$clientName, $products, $language, $countryCodes` but these will be consumed from
the config file `config/stripe-plaid.php` if aren't passed through.

```php
use AlexVargash\LaravelStripePlaid\StripePlaid;

$clientUserId = 'your_end_user_id';

$stripePlaid = new StripePlaid();
$linkToken   = $stripePlaid->createLinkToken($clientUserId);
```

Now exchange the `public_token` and `account_id` that are returned by [Plaid Link](https://plaid.com/docs/stripe/#step3).

```php
use AlexVargash\LaravelStripePlaid\StripePlaid;

$accountId   = 'plaid_link_account_id';
$publicToken = 'plaid_link_public_token';

$stripePlaid = new StripePlaid();
$stripeToken = $stripePlaid->getStripeToken($publicToken, $accountId);
```

After that you can process the payment with the `$stripeToken` as you do with a Stripe Elements token.

The link creation and the exchange can be done with a Facade too.

```php
use AlexVargash\LaravelStripePlaid\Facades\StripePlaid;

$clientUserId = 'your_end_user_id';

$linkToken    = StripePlaid::createLinkToken($clientUserId);
```

```php
use AlexVargash\LaravelStripePlaid\Facades\StripePlaid;

$accountId   = 'plaid_link_account_id';
$publicToken = 'plaid_link_public_token';

$stripeToken = StripePlaid::getStripeToken($publicToken, $accountId);
```

Alternatively the Plaid keys can be set prior token exchange, this is handy when multiple Plaid accounts are going to be used.

```php
use AlexVargash\LaravelStripePlaid\StripePlaid;

$secret      = 'your_plaid_secret_key';
$clientId    = 'your_plaid_client_id';
$environment = 'sandbox';
$accountId   = 'plaid_link_account_id';
$publicToken = 'plaid_link_public_token';

$stripeToken = StripePlaid::make($secret, $clientId, $environment)->getStripeToken($publicToken, $accountId);
```

### Exceptions

When an error occurs a `PlaidException` will be thrown. You can catch the `PlaidException` on the `Exceptions\Handler.php` file:

```php
public function render($request, Exception $exception)
{
    if ($exception instanceof \AlexVargash\LaravelStripePlaid\Exceptions\PlaidException) {
        // Manage exception here ...
    }

    return parent::render($request, $exception);
}
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)

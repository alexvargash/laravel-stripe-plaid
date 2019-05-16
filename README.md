# Laravel Stripe Plaid

Simple package for creating a Stripe Bank Account Token from Plaid Link.

## Installation

[![Build Status](https://travis-ci.org/alexvargash/laravel-stripe-plaid.svg?branch=master)](https://travis-ci.org/alexvargash/laravel-stripe-plaid)
[![StyleCI](https://github.styleci.io/repos/185878123/shield?branch=master)](https://github.styleci.io/repos/185878123)

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
```

## Usage

First, add the Plaid keys and environment to the `config/stripe-plaid.php` file or on your `.env`.

```bash
PLAID_ENVIRONMENT=sandbox
PLAID_SECRET=your_plaid_secret_key
PLAID_CLIENT_ID=your_plaid_client_id
```

Now exchange the `public_token` and `account_id` that are returned by [Plaid Link](https://plaid.com/docs/stripe/#step3).

```php
use AlexVargash\LaravelStripePlaid\StripePlaid;

$accountId = 'plaid_link_account_id';
$publicToken = 'plaid_link_public_token';

$stripePlaid = new StripePlaid();
$stripeToken = $stripePlaid->getStripeToken($publicToken, $accountId);
```

After that you can process the payment with the `$stripeToken` as you do with a Stripe Elements token.

The exchange can be done with a Facade too.

```php
use AlexVargash\LaravelStripePlaid\Facades\StripePlaid;

$accountId = 'plaid_link_account_id';
$publicToken = 'plaid_link_public_token';

$stripeToken = StripePlaid::getStripeToken($publicToken, $accountId);
```

Alternatively the Plaid keys can be set prior token exchange, this is handy when multiple Plaid accounts are going to be used.

```php
$secret = 'your_plaid_secret_key';
$clientId = 'your_plaid_client_id';
$environment = 'sandbox';
$accountId = 'plaid_link_account_id';
$publicToken = 'plaid_link_public_token';

$stripeToken = StripePlaid::make($secret, $clientId, $environment)->getStripeToken($publicToken, $accountId);
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)

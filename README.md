# Laravel Stripe Plaid

Simple package for creating a Stripe Bank Account Token from a Plaid token.

## Installation

Require the package using composer:

```bash
composer require alexvargash/laravel-stripe-plaid
```

## Usage

Set an array with your Plaid keys:

```php
use AlexVargash\LaravelStripePlaid\StripePlaid;

$keys = [
    'secret' => 'your_secret_key',
    'client_id' => 'your_client_id',
    'account_id' => 'account_id_returned_by_plaid',
    'public_token' => 'public_token_returned_by_plaid',
    'environment' => 'sandbox', // sandbox | production
];

$stripePlaid = new StripePlaid($keys);
$stripeToken = $stripePlaid->getStripeToken();
```

After that you can process the payment with the `$stripeToken` as you do with the stripe elements token.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)

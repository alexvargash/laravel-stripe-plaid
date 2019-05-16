<?php

namespace AlexVargash\LaravelStripePlaid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use AlexVargash\LaravelStripePlaid\Exceptions\PlaidException;

class StripePlaid
{
    private $client;
    private $secret;
    private $clientId;
    private $environment;
    private $exchangeUrl;
    private $accountTokenUrl;

    /**
     * Set the global variables and validate the Plaid keys.
     *
     * @param  string  $secret
     * @param  string  $clientId
     * @param  string  $environment
     * @param  GuzzleHttp\Client  $client
     */
    public function __construct($secret = null, $clientId = null, $environment = null, Client $client = null)
    {
        $this->client = $client ?: new Client();
        $this->secret = $secret ?: config('stripe-plaid.secret');
        $this->clientId = $clientId ?: config('stripe-plaid.client_id');
        $this->environment = $environment ?: config('stripe-plaid.environment');
        $this->exchangeUrl = "https://{$this->environment}.plaid.com/item/public_token/exchange";
        $this->accountTokenUrl = "https://{$this->environment}.plaid.com/processor/stripe/bank_account_token/create";
        $this->validateKeys();
    }

    /**
     * Create a new instance of the class StripePlaid.
     *
     * @param  string  $secret
     * @param  string  $clientId
     * @param  string  $environment
     * @return  AlexVargash\LaravelStripePlaid\StripePlaid
     */
    public static function make($secret, $clientId, $environment, Client $client = null)
    {
        return new static($secret, $clientId, $environment, $client);
    }

    /**
     * Review if any key is null and verify that the environment is one of the
     * accepted by Plaid.
     */
    public function validateKeys()
    {
        if (in_array(null, [$this->secret, $this->clientId, $this->environment])) {
            throw PlaidException::missingKeys();
        }

        if (! in_array($this->environment, ['sandbox', 'development', 'production'])) {
            throw PlaidException::invalidEnvironment();
        }
    }

    /**
     * Call the exchange token and create stripe token functions.
     *
     * @param  string  $publicToken
     * @param  string  $accountId
     * @return string
     */
    public function getStripeToken($publicToken, $accountId)
    {
        $accessToken = $this->exchangePublicToken($publicToken);

        return $this->createStripeBankAccountToken($accessToken, $accountId);
    }

    /**
     * Exchange the public token for an access token.
     *
     * @param  string  $publicToken
     * @return string  access_token
     */
    public function exchangePublicToken($publicToken)
    {
        $params = [
            'secret' => $this->secret,
            'client_id' => $this->clientId,
            'public_token' => $publicToken,
        ];

        return $this->makeHttpRequest($this->exchangeUrl, $params)->access_token;
    }

    /**
     * Get the Stripe bank account token.
     *
     * @param  string  $accessToken
     * @param  string  $accountId
     * @return string  stripe_bank_account_token
     */
    public function createStripeBankAccountToken($accessToken, $accountId)
    {
        $btokParams = [
            'access_token' => $accessToken,
            'secret' => $this->secret,
            'client_id' => $this->clientId,
            'account_id' => $accountId,
        ];

        return $this->makeHttpRequest($this->accountTokenUrl, $btokParams)->stripe_bank_account_token;
    }

    /**
     * Create a POST request to the provided url with the corresponding
     * parameters.
     *
     * @param  string  $url
     * @param  string  $params
     * @return json
     */
    public function makeHttpRequest($url, $params)
    {
        try {
            $response = $this->client->request('POST', $url, [
                'headers' => ['Content-Type' => 'application/json'],
                'connect_timeout' => 30,
                'timeout' => 80,
                'body' => json_encode($params),
            ]);
        } catch (ClientException $e) {
            throw PlaidException::badRequest($e->getResponse()->getBody());
        } catch (ServerException $e) {
            throw PlaidException::badRequest($e->getResponse()->getBody());
        }

        return json_decode($response->getBody());
    }
}

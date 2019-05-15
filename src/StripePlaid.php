<?php

namespace AlexVargash\LaravelStripePlaid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use AlexVargash\LaravelStripePlaid\Helpers\PlaidKeys;
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
     * @param  String  $secret
     * @param  String  $clientId
     * @param  String  $environment
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
     * @param  String  $secret
     * @param  String  $clientId
     * @param  String  $environment
     * @return  AlexVargash\LaravelStripePlaid\StripePlaid
     */
    public static function make($secret, $clientId, $environment)
    {
        return new static($secret, $clientId, $environment);
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

        if (!in_array($this->environment, ['sandbox', 'production'])) {
            throw PlaidException::invalidEnvironment();
        }
    }

    /**
     * Call the exchange token and create stripe token functions.
     *
     * @param  String  $publicToken
     * @param  String  $accountId
     * @return String
     */
    public function getStripeToken($publicToken, $accountId)
    {
        $accessToken = $this->exchangePublicToken($publicToken);

        return $this->createStripeBankAccountToken($accessToken, $accountId);
    }

    /**
     * Exchange the public token for an access token.
     *
     * @param  String  $publicToken
     * @return String  access_token
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
     * @param  String  $accessToken
     * @param  String  $accountId
     * @return String  stripe_bank_account_token
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
     * @param  String  $url
     * @param  String  $params
     * @return json
     */
    public function makeHttpRequest($url, $params)
    {
        try {
            $response = $this->client->request('POST', $url, [
                'headers' => ['Content-Type' => 'application/json'],
                'connect_timeout' => 30,
                'timeout' => 80,
                'body' => json_encode($params)
            ]);
        } catch (ClientException $e) {
            throw PlaidException::badRequest($e->getResponse()->getBody());
        } catch (ServerException $e) {
            throw PlaidException::badRequest($e->getResponse()->getBody());
        }

        return json_decode($response->getBody());
    }
}

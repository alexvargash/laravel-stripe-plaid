<?php

namespace AlexVargash\LaravelStripePlaid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use AlexVargash\LaravelStripePlaid\Helpers\PlaidKeys;
use AlexVargash\LaravelStripePlaid\Exceptions\PlaidException;

class StripePlaid
{
    private $secret;
    private $clientId;
    private $client;
    private $environment;
    private $exchangeUrl;
    private $accountTokenUrl;

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

    public static function make($secret, $clientId, $environment)
    {
        return new static($secret, $clientId, $environment);
    }

    public function validateKeys()
    {
        if (in_array(null, [$this->secret, $this->clientId, $this->environment])) {
            throw PlaidException::missingKeys();
        }

        if (!in_array($this->environment, ['sandbox', 'production'])) {
            throw PlaidException::invalidEnvironment();
        }
    }

    public function getStripeToken($publicToken, $accountId)
    {
        $accessToken = $this->exchangePublicToken($publicToken);

        return $this->createStripeBankAccountToken($accessToken, $accountId);
    }

    public function exchangePublicToken($publicToken)
    {
        $params = [
            'secret' => $this->secret,
            'client_id' => $this->clientId,
            'public_token' => $publicToken,
        ];

        return $this->makeHttpRequest($this->exchangeUrl, $params)->access_token;
    }

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

<?php

namespace AlexVargash\LaravelStripePlaid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use AlexVargash\LaravelStripePlaid\Helpers\PlaidKeys;
use AlexVargash\LaravelStripePlaid\Exceptions\PlaidException;

class StripePlaid
{
    private $keys;
    private $client;
    private $exchangeUrl;
    private $accountTokenUrl;

    public function __construct($keys, Client $client = null)
    {
        $this->keys = PlaidKeys::validate($keys);
        $this->client = $client ?: new Client();
        $this->exchangeUrl = "https://{$keys['environment']}.plaid.com/item/public_token/exchange";
        $this->accountTokenUrl = "https://{$keys['environment']}.plaid.com/processor/stripe/bank_account_token/create";
    }

    public function getStripeToken()
    {
        $accessToken = $this->exchangePublicToken();

        return $this->createStripeBankAccountToken($accessToken);
    }

    public function exchangePublicToken()
    {
        $params = [
            'secret' => $this->keys['secret'],
            'client_id' => $this->keys['client_id'],
            'public_token' => $this->keys['public_token'],
        ];

        return $this->makeHttpRequest($this->exchangeUrl, $params)->access_token;
    }

    public function createStripeBankAccountToken($accessToken)
    {
        $btokParams = [
            'access_token' => $accessToken,
            'secret' => $this->keys['secret'],
            'client_id' => $this->keys['client_id'],
            'account_id' => $this->keys['account_id'],
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
            dd($e);
            throw PlaidException::badRequest($e->getResponse()->getBody());
        } catch (ServerException $e) {
            throw PlaidException::badRequest($e->getResponse()->getBody());
        }

        return json_decode($response->getBody());
    }
}

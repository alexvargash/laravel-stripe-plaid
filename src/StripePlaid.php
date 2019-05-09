<?php

namespace AlexVargash\LaravelStripePlaid;

class StripePlaid
{
	private $keys;
	private $exchangeUrl;
	private $accountTokenUrl;

	public function __construct($keys)
	{
		$this->keys = $keys;
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

		//var_dump($this->makeHttpRequest($this->exchangeUrl, $params));
		//return $this->makeHttpRequest($this->exchangeUrl, $params);
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
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 80);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if(!$result = curl_exec($ch)) {
		   trigger_error(curl_error($ch));
		}
		curl_close($ch);

		return json_decode($result);
	}
}

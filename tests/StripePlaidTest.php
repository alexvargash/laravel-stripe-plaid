<?php

namespace AlexVargash\LaravelStripePlaid\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use AlexVargash\LaravelStripePlaid\StripePlaid;
use AlexVargash\LaravelStripePlaid\Exceptions\PlaidException;

class StripePlaidTest extends TestCase
{
    /** @test */
    public function it_returns_a_stripe_token()
    {
        $keys = [
            'secret' => '5c74834hs3iag5as9884s1c8g9987d',
            'client_id' => '1dd5243s03634t228712ss23',
            'account_id' => 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH',
            'public_token' => 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu',
            'environment' => 'sandbox',
        ];

        $mock = new MockHandler([
            new Response(200, [], '{ "access_token": "access-sandbox-2a8r3199-2246-9327-s4w2-8gf25g0538d3", "item_id": "fDv3IHF6sKfdOos2OlsuRpkhfimIOsgaTcMGSR", "request_id": "ww2lHsaExF5a7s1" }'),
            new Response(200, [], '{ "request_id": "735IqPIxDgPJSTV", "stripe_bank_account_token": "btok_6IJuhsDmxnxDFFxYKGEnxc4a" }'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $stripePlaid = new StripePlaid($keys, $client);
        $stripeToken = $stripePlaid->getStripeToken();

        $this->assertSame('btok_6IJuhsDmxnxDFFxYKGEnxc4a', $stripeToken);
    }

    /** @test */
    public function it_needs_to_have_the_plaid_secret_key()
    {
        $keys = [
            'client_id' => '1dd5243s03634t228712ss23',
            'account_id' => 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH',
            'public_token' => 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu',
            'environment' => 'sandbox',
        ];

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage("The Plaid 'secret' key is missing.");

        $stripePlaid = new StripePlaid($keys);
        $stripeToken = $stripePlaid->getStripeToken();
    }

    /** @test */
    public function it_needs_to_have_the_plaid_client_id()
    {
        $keys = [
            'secret' => '5c74834hs3iag5as9884s1c8g9987d',
            'account_id' => 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH',
            'public_token' => 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu',
            'environment' => 'sandbox',
        ];

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage("The Plaid 'client_id' key is missing.");

        $stripePlaid = new StripePlaid($keys);
        $stripeToken = $stripePlaid->getStripeToken();
    }

    /** @test */
    public function it_needs_to_have_the_plaid_account_id()
    {
        $keys = [
            'secret' => '5c74834hs3iag5as9884s1c8g9987d',
            'client_id' => '1dd5243s03634t228712ss23',
            'public_token' => 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu',
            'environment' => 'sandbox',
        ];

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage("The Plaid 'account_id' key is missing.");

        $stripePlaid = new StripePlaid($keys);
        $stripeToken = $stripePlaid->getStripeToken();
    }

    /** @test */
    public function it_needs_to_have_the_plaid_public_token()
    {
        $keys = [
            'secret' => '5c74834hs3iag5as9884s1c8g9987d',
            'client_id' => '1dd5243s03634t228712ss23',
            'account_id' => 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH',
            'environment' => 'sandbox',
        ];

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage("The Plaid 'public_token' key is missing.");

        $stripePlaid = new StripePlaid($keys);
        $stripeToken = $stripePlaid->getStripeToken();
    }

    /** @test */
    public function it_needs_to_have_the_plaid_environment()
    {
        $keys = [
            'secret' => '5c74834hs3iag5as9884s1c8g9987d',
            'client_id' => '1dd5243s03634t228712ss23',
            'account_id' => 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH',
            'public_token' => 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu',
        ];

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage("The Plaid 'environment' key is missing.");

        $stripePlaid = new StripePlaid($keys);
        $stripeToken = $stripePlaid->getStripeToken();
    }

    /** @test */
    public function the_plaid_evironment_must_be_sandbox_or_production()
    {
        $keys = [
            'secret' => '5c74834hs3iag5as9884s1c8g9987d',
            'client_id' => '1dd5243s03634t228712ss23',
            'account_id' => 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH',
            'public_token' => 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu',
            'environment' => 'staging',
        ];

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage("The Plaid environment must be: 'sandbox' or 'production'.");

        $stripePlaid = new StripePlaid($keys);
        $stripeToken = $stripePlaid->getStripeToken();
    }
}

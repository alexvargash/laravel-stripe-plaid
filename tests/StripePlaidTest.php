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
        $environment = 'sandbox';
        $clientId = '1dd5243s03634t228712ss23';
        $secret = '5c74834hs3iag5as9884s1c8g9987d';
        $accountId = 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH';
        $publicToken = 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu';

        $mock = new MockHandler([
            new Response(200, [], '{ "access_token": "access-sandbox-2a8r3199-2246-9327-s4w2-8gf25g0538d3", "item_id": "fDv3IHF6sKfdOos2OlsuRpkhfimIOsgaTcMGSR", "request_id": "ww2lHsaExF5a7s1" }'),
            new Response(200, [], '{ "request_id": "735IqPIxDgPJSTV", "stripe_bank_account_token": "btok_6IJuhsDmxnxDFFxYKGEnxc4a" }'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $stripeToken = StripePlaid::make($secret, $clientId, $environment, $client)->getStripeToken($publicToken, $accountId);

        $this->assertSame('btok_6IJuhsDmxnxDFFxYKGEnxc4a', $stripeToken);
    }

    /** @test */
    public function the_public_token_should_not_be_expired()
    {
        $environment = 'sandbox';
        $clientId = '1dd5243s03634t228712ss23';
        $secret = '5c74834hs3iag5as9884s1c8g9987d';
        $accountId = 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH';
        $publicToken = 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu';

        $mock = new MockHandler([
            new Response(400, [], '{"display_message": null, "error_code": "INVALID_PUBLIC_TOKEN", "error_message": "provided public token is expired. Public tokens expire 30 minutes after creation at which point they can no longer be exchanged", "error_type": "INVALID_INPUT", "request_id": "wfn92ATB3EC83m5", "suggested_action": null }'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage('{"display_message": null, "error_code": "INVALID_PUBLIC_TOKEN", "error_message": "provided public token is expired. Public tokens expire 30 minutes after creation at which point they can no longer be exchanged", "error_type": "INVALID_INPUT", "request_id": "wfn92ATB3EC83m5", "suggested_action": null }');

        $stripeToken = StripePlaid::make($secret, $clientId, $environment, $client)->getStripeToken($publicToken, $accountId);
    }

    /** @test */
    public function the_plaid_credentials_should_be_correct()
    {
        $environment = 'sandbox';
        $clientId = '1dd5243s03634t228712ss23';
        $secret = '5c74834hs3iag5as9884s1c8g9987d';
        $accountId = 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH';
        $publicToken = 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu';

        $mock = new MockHandler([
            new Response(400, [], '{"display_message": null, "error_code": "INVALID_API_KEYS", "error_message": "invalid client_id or secret provided", "error_type": "INVALID_INPUT", "request_id": "Qo8n3fkIGVrFj0j", "suggested_action": null }'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage('{"display_message": null, "error_code": "INVALID_API_KEYS", "error_message": "invalid client_id or secret provided", "error_type": "INVALID_INPUT", "request_id": "Qo8n3fkIGVrFj0j", "suggested_action": null }');

        $stripeToken = StripePlaid::make($secret, $clientId, $environment, $client)->getStripeToken($publicToken, $accountId);
    }

    /** @test */
    public function the_plaid_evironment_must_be_sandbox_or_production()
    {
        $environment = 'staging';
        $clientId = '1dd5243s03634t228712ss23';
        $secret = '5c74834hs3iag5as9884s1c8g9987d';
        $accountId = 'aAdsDrJBeKvN43N9S2Y2UGhuXs2vd6JNsUCDH';
        $publicToken = 'public-sandbox-8765c42w-2nd1-8976-432s-x37m6kjd78cu';

        $this->expectException(PlaidException::class);
        $this->expectExceptionMessage('{ "display_message": null, "error_code": "INVALID_ENVIRONMENT", "error_message": "The environment must be: sandbox, development or production.", "error_type": "INVALID_INPUT" }');

        $stripeToken = StripePlaid::make($secret, $clientId, $environment)->getStripeToken($publicToken, $accountId);
    }
}

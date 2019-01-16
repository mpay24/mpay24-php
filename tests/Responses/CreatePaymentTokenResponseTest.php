<?php

namespace Mpay24Test\Responses;

use Mpay24\Responses\CreatePaymentTokenResponse;
use PHPUnit\Framework\TestCase;

/**
 * Class CreatePaymentTokenResponseTest
 * @package    Mpay24Test\Responses
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource CreatePaymentTokenResponseTest.php
 * @license    MIT
 */
class CreatePaymentTokenResponseTest extends TestCase
{
    public function testConstructStatusOkWithRedirect()
    {
        $response = new CreatePaymentTokenResponse(file_get_contents(__DIR__ . '/_files/create-payment-token.response.ok.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertSame('REDIRECT', $response->getReturnCode());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());

        $this->assertSame('random+token3COK46edPNN68cg1DRFkxoXkbcRW4Owp', $response->getToken());
        $this->assertSame('random1api2key3nyp8xbmsaxdrudwx3zm6cgzvoeskccz7vnjdm35wwjusnjfce', $response->getApiKey());
        $this->assertSame('https://example.com/location', $response->getLocation());
    }

    public function testConstructErrorProfileNotFound()
    {
        $response = new CreatePaymentTokenResponse(file_get_contents(__DIR__ . '/_files/create-payment-token.response.error.profile-not-found.xml'));
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertSame('PROFILE_NOT_FOUND', $response->getReturnCode());

        $this->assertSame(20, $response->getErrNo());

        $this->assertNull($response->getErrText());
        $this->assertNull($response->getToken());
        $this->assertNull($response->getApiKey());
        $this->assertNull($response->getLocation());
    }

    public function testConstructErrorPTypeMismatch()
    {
        $response = new CreatePaymentTokenResponse(file_get_contents(__DIR__ . '/_files/create-payment-token.response.error.ptype-mismatch.xml'));
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertSame("Required field \"status\" is missing. SOAP-ENV:Client - Validation constraint violation: data type mismatch in element 'pType'", $response->getReturnCode());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());
        $this->assertNull($response->getToken());
        $this->assertNull($response->getApiKey());
        $this->assertNull($response->getLocation());
    }
}

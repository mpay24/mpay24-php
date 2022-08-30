<?php

namespace Mpay24Test\Responses;

use Mpay24\Responses\CreateApplePayTokenResponse;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateApplePayTokenResponseTest
 * @package    Mpay24Test\Responses
 *
 * @author     Unzer Austria GmbH <online.support.at@unzer.com>
 * @author     Stefan Polzer <develop@ps-webdesign.com>, Milko Daskalov <milko.daskalov@unzer.com>
 * @filesource CreateApplePayTokenResponseTest.php
 * @license    MIT
 */
class CreateApplePayTokenResponseTest extends TestCase
{
    public function testConstructStatusOkWithRedirect()
    {
        $response = new CreateApplePayTokenResponse(file_get_contents(__DIR__ . '/_files/create-apple-pay-token.response.ok.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertSame('OK', $response->getReturnCode());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());

        $this->assertSame('4arXhSxoAbvPPjM2/GqzEWii1y0kb7k2XqbFfaWVFA8=', $response->getToken());
        $this->assertSame('674292576d5ae215b2a683e2a789755c634560b13b81a0157e3251c4e052b057', $response->getApiKey());

        $this->assertTrue($response->hasNoException());
        $this->assertTrue($response->hasNoError());
        $this->assertTrue($response->hasStatusOk());
    }

    public function testConstructErrorCurrencyNotCorrect()
    {
        $response = new CreateApplePayTokenResponse(file_get_contents(__DIR__ . '/_files/create-apple-pay-token.response.error.currency-not-correct.xml'));
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertSame('CURRENCY_NOT_CORRECT', $response->getReturnCode());

        $this->assertNull($response->getErrText());
        $this->assertNull($response->getToken());
        $this->assertNull($response->getApiKey());

        $this->assertTrue($response->hasNoException());
        $this->assertFalse($response->hasNoError());
        $this->assertFalse($response->hasStatusOk());
    }
}

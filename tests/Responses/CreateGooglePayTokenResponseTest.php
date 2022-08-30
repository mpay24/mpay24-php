<?php

namespace Mpay24Test\Responses;

use Mpay24\Responses\CreateGooglePayTokenResponse;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateGooglePayTokenResponseTest
 * @package    Mpay24Test\Responses
 *
 * @author     Unzer Austria GmbH <online.support.at@unzer.com>
 * @author     Stefan Polzer <develop@ps-webdesign.com>, Milko Daskalov <milko.daskalov@unzer.com>
 * @filesource CreateGooglePayTokenResponseTest.php
 * @license    MIT
 */
class CreateGooglePayTokenResponseTest extends TestCase
{
    public function testConstructStatusOkWithRedirect()
    {
        $response = new CreateGooglePayTokenResponse(file_get_contents(__DIR__ . '/_files/create-google-pay-token.response.ok.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertSame('OK', $response->getReturnCode());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());

        $this->assertSame('YnBK851+oVyb69nXOHo10yXq0MWU3g7VuesjY6i5sMY=', $response->getToken());
        $this->assertSame('2976790ea6022d7755d58c9068b3f94ea5822a767d4b074e4fdebb8b13e41edd', $response->getApiKey());

        $this->assertTrue($response->hasNoException());
        $this->assertTrue($response->hasNoError());
        $this->assertTrue($response->hasStatusOk());
    }

    public function testConstructErrorCurrencyNotCorrect()
    {
        $response = new CreateGooglePayTokenResponse(file_get_contents(__DIR__ . '/_files/create-google-pay-token.response.error.currency-not-correct.xml'));
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

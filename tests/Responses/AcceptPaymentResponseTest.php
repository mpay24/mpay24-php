<?php

namespace Mpay24Test\Responses;

use Mpay24\Responses\AcceptPaymentResponse;
use PHPUnit\Framework\TestCase;

class AcceptPaymentResponseTest extends TestCase
{
    public function testConstructStatusOk()
    {
        $response = new AcceptPaymentResponse(file_get_contents(__DIR__.'/_files/accept-payment.response.ok.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertEquals('OK', $response->getReturnCode());
        $this->assertSame(12345, $response->getMpayTid());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());
        $this->assertNull($response->getLocation());

        $this->assertTrue($response->hasNoError());
        $this->assertTrue($response->hasStatusOk());
    }

    public function testConstructStatusError()
    {
        $response = new AcceptPaymentResponse(file_get_contents(__DIR__.'/_files/accept-payment.response.error.xml'));
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertEquals('EXTERNAL_ERROR', $response->getReturnCode());
        $this->assertSame(12345, $response->getMpayTid());

        $this->assertSame(100, $response->getErrNo());
        $this->assertSame('cvv2/cvc2 falsch (cvv2/cvc2 failure)', $response->getErrText());
        $this->assertNull($response->getLocation());

        $this->assertFalse($response->hasNoError());
        $this->assertFalse($response->hasStatusOk());
    }

    public function testConstructXmlResponseEmpty()
    {
        $response = new AcceptPaymentResponse('');
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertEquals('The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!', $response->getReturnCode());
        $this->assertNull($response->getMpayTid());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());
        $this->assertNull($response->getLocation());

        $this->assertFalse($response->hasNoError());
        $this->assertFalse($response->hasStatusOk());
    }

    public function testConstructInvalidXml()
    {
        $response = new AcceptPaymentResponse('<?xml version="1.0" encoding="UTF-8"?><xml>invalidxml');
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertSame('Unknown Error', $response->getReturnCode());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());
        $this->assertNull($response->getLocation());

        $this->assertFalse($response->hasNoError());
        $this->assertFalse($response->hasStatusOk());
    }

    public function testConstructUnauthorized()
    {
        $response = new AcceptPaymentResponse(file_get_contents(__DIR__.'/_files/accept-payment.response.unauthorized.xml'));

        $this->assertSame('ERROR', $response->getStatus());
        $this->assertSame('401 Unauthorized: check your merchant ID and password', $response->getReturnCode());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());
        $this->assertNull($response->getLocation());

        $this->assertFalse($response->hasNoError());
        $this->assertFalse($response->hasStatusOk());
    }
}
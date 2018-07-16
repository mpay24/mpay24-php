<?php

namespace Mpay24Test\Responses;

use Mpay24\Responses\CreateCustomerResponse;
use PHPUnit\Framework\TestCase;

class CreateCustomerResponseTest extends TestCase
{
    public function testConstructStatusOk()
    {
        $response = new CreateCustomerResponse(file_get_contents(__DIR__.'/_files/create-customer.response.ok.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertEquals('PROFILE_CREATED', $response->getReturnCode());
        $this->assertNull($response->getMpayTid());

        $this->assertNull($response->getErrNo());
        $this->assertNull($response->getErrText());
        $this->assertNull($response->getLocation());

        $this->assertTrue($response->hasNoError());
        $this->assertTrue($response->hasStatusOk());
    }

    public function testConstructStatusError()
    {
        $response = new CreateCustomerResponse(file_get_contents(__DIR__.'/_files/create-customer.response.error.xml'));
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertEquals('PROFILEID_NOT_CORRECT', $response->getReturnCode());
        $this->assertNull($response->getMpayTid());

        $this->assertSame(7, $response->getErrNo());
        $this->assertNull($response->getErrText());
        $this->assertNull($response->getLocation());

        $this->assertFalse($response->hasNoError());
        $this->assertFalse($response->hasStatusOk());
    }
}
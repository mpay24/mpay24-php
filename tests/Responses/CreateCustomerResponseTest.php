<?php

namespace Mpay24Test\Responses;

use Mpay24\Responses\CreateCustomerResponse;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateCustomerResponseTest
 * @package Mpay24Test\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource CreateCustomerResponseTest.php
 * @license    MIT
 */
class CreateCustomerResponseTest extends TestCase
{
    public function testConstructStatusOk()
    {
        $response = new CreateCustomerResponse(file_get_contents(__DIR__ . '/_files/create-customer.response.ok.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertEquals('PROFILE_CREATED', $response->getReturnCode());

        $this->assertNull($response->getErrNo());

        $this->assertTrue($response->hasNoException());
        $this->assertTrue($response->hasNoError());
        $this->assertTrue($response->hasStatusOk());
    }

    public function testConstructStatusError()
    {
        $response = new CreateCustomerResponse(file_get_contents(__DIR__ . '/_files/create-customer.response.error.xml'));
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertEquals('PROFILEID_NOT_CORRECT', $response->getReturnCode());

        $this->assertSame(7, $response->getErrNo());

        $this->assertTrue($response->hasNoException());
        $this->assertFalse($response->hasNoError());
        $this->assertFalse($response->hasStatusOk());
    }
}

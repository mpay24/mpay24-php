<?php

namespace Mpay24Test\Responses;

use Mpay24\Responses\DeleteProfileResponse;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteProfileResponse
 * @package    Mpay24Test\Responses
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource DeleteProfileResponse.php
 * @license    MIT
 */
class DeleteProfileResponseTest extends TestCase
{
    public function testConstructStatusOk()
    {
        $response = new DeleteProfileResponse(file_get_contents(__DIR__ . '/_files/delete-profile.response.ok.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertSame('OK', $response->getReturnCode());

        $this->assertTrue($response->hasNoException());
        $this->assertTrue($response->hasNoError());
        $this->assertTrue($response->hasStatusOk());
    }

    public function testConstructErrorProfileNotFound()
    {
        $response = new DeleteProfileResponse(file_get_contents(__DIR__ . '/_files/delete-profile.response.error.xml'));
        $this->assertSame('ERROR', $response->getStatus());
        $this->assertSame('PROFILE_NOT_FOUND', $response->getReturnCode());

        $this->assertTrue($response->hasNoException());
        $this->assertFalse($response->hasNoError());
        $this->assertFalse($response->hasStatusOk());
    }
}

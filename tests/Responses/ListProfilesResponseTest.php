<?php

namespace Mpay24Test\Responses;

use Mpay24\Responses\ListProfilesResponse;
use PHPUnit\Framework\TestCase;

/**
 * Class ListProfilesResponseTest
 * @package    Mpay24Test\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource ListProfilesResponseTest.php
 * @license    MIT
 */
class ListProfilesResponseTest extends TestCase
{
    public function testConstructSingleCustomerWithNoPaymentProfiles()
    {
        $response = new ListProfilesResponse(file_get_contents(__DIR__ . '/_files/list-profiles.response.no-payment-profiles.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertSame('OK', $response->getReturnCode());

        $this->assertSame(1, $response->getProfileCount());
        $this->assertSame(1, $response->getTotalNumber());

        $expectedProfiles = [
            [
                'customerID' => '1234',
                'updated'    => '2018-06-05T10:54:02Z',
            ]
        ];

        $this->assertSame($expectedProfiles, $response->getProfiles());
        $this->assertSame($expectedProfiles[0], $response->getProfile(0));

        $this->assertTrue($response->hasNoException());
        $this->assertTrue($response->hasNoError());
        $this->assertTrue($response->hasStatusOk());
    }

    public function testConstructSingleCustomerWithPaymentProfiles()
    {
        $response = new ListProfilesResponse(file_get_contents(__DIR__ . '/_files/list-profiles.response.with-payment-profiles.xml'));
        $this->assertSame('OK', $response->getStatus());
        $this->assertSame('OK', $response->getReturnCode());

        $this->assertSame(1, $response->getProfileCount());
        $this->assertSame(1, $response->getTotalNumber());

        $profile = $response->getProfile(0);

        $this->assertSame('1234', $profile['customerID']);
        $this->assertSame('2018-06-05T10:54:02Z', $profile['updated']);

        $this->assertContains('2018-06-01T12:05:08Z', $profile['payment']);
        $this->assertContains('************1111', $profile['payment']);
        $this->assertContains('2025-05-01', $profile['payment']);

        $this->assertCount(2, $profile['paymentProfiles']);

        $expectedPaymentProfiles = [
            [
                'pMethodID'  => '5',
                'profileID'  => '',
                'updated'    => '2018-06-01T12:05:08Z',
                'identifier' => '************1111',
                'expires'    => '2025-05-01',
            ],
            [
                'pMethodID'  => '5',
                'profileID'  => 'testprofile3',
                'updated'    => '2018-07-02T12:45:35Z',
                'identifier' => '************1234',
                'expires'    => '2026-06-02',
            ]
        ];

        $this->assertSame($expectedPaymentProfiles, $profile['paymentProfiles']);

        $this->assertTrue($response->hasNoException());
        $this->assertTrue($response->hasNoError());
        $this->assertTrue($response->hasStatusOk());
    }
}

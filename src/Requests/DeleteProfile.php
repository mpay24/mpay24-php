<?php

namespace Mpay24\Requests;

/**
 * The DeleteProfile class creates the body for the SOAP request.
 *
 * Class DeleteProfile
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource DeleteProfile.php
 * @license    MIT
 */
class DeleteProfile extends AbstractRequest
{
    /**
     * @var string
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $profileId;

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @param string $profileId
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    /**
     * Build the request document body.
     */
    protected function build()
    {
        $operation = $this->buildOperation('DeleteProfile');

        $merchantID = $this->document->createElement(
            'merchantID',
            $this->merchantId
        );
        $operation->appendChild($merchantID);

        $customerId = $this->document->createElement(
            'customerID',
            $this->customerId
        );
        $operation->appendChild($customerId);

        if ($this->profileId) {
            $profileId = $this->document->createElement(
                'profileID',
                $this->profileId
            );
            $operation->appendChild($profileId);
        }
    }
}

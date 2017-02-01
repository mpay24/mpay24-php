<?php

namespace Mpay24\Responses;

/**
 * The PaymentResponse class contains a generalResponse object and the location(URL), which will be used for the payment session
 *
 * Class PaymentTokenResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource PaymentResponse.php
 * @license    MIT
 */
class PaymentResponse extends GeneralResponse
{
    /**
     * An URL (of the mPAY24 payment fenster), where the customer would be redirected to, in case of successfull request
     *
     * @var string
     */
    protected $location;

    /**
     * The unique ID returned by mPAY24 for every transaction
     *
     * @var string
     */
    protected $mpay24Tid;

    /**
     * Sets the values for a payment from the response from mPAY24: mPAY transaction ID, error number and location (URL)
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {
            if ($this->responseAsDom->getElementsByTagName('location')->length != 0) {
                $this->location = $this->responseAsDom->getElementsByTagName('location')->item(0)->nodeValue;
            }

            if ($this->responseAsDom->getElementsByTagName('mpayTID')->length != 0) {
                $this->mpay24Tid = $this->responseAsDom->getElementsByTagName('mpayTID')->item(0)->nodeValue;
            }
        }
    }

    /**
     * Get the location (URL), returned from mPAY24
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get the unique ID, returned from mPAY24
     *
     * @return string
     */
    public function getMpay24Tid()
    {
        return $this->mpay24Tid;
    }
}

<?php

namespace Mpay24\Responses;

/**
 * The ManagePaymentResponse class contains a generalResponse object and the mPAYTID and/or tid of the transaction which was managed
 *
 * Class ManagePaymentResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource ManagePaymentResponse.php
 * @license    MIT
 */
class ManagePaymentResponse extends GeneralResponse
{
    /**
     * The mPAY transaction ID
     *
     * @var string
     */
    protected $mpay24Tid;

    /**
     * The transaction ID of the shop
     *
     * @var string
     */
    protected $tid;

    /**
     * Sets the values for a payment from the response from mPAY24: mPAY transaction IDand transaction ID from the shop
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {
            if ($this->responseAsDom->getElementsByTagName('mpayTID')->length != 0
                && $this->responseAsDom->getElementsByTagName('tid')->length != 0
            ) {
                $this->mpay24Tid = $this->responseAsDom->getElementsByTagName('mpayTID')->item(0)->nodeValue;
                $this->tid       = $this->responseAsDom->getElementsByTagName('tid')->item(0)->nodeValue;
            }
        }
    }

    /**
     * Get the mPAY transaction ID, returned from mPAY24
     *
     * @return string
     */
    public function getMpay24Tid()
    {
        return $this->mpay24Tid;
    }

    /**
     * Get the transaction ID of the shop, returned from mPAY24
     *
     * @return string
     */
    public function getTid()
    {
        return $this->tid;
    }
}

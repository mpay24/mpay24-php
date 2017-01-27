<?php

namespace mPay24\Responses;

use DOMDocument;

/**
 * The ManagePaymentResponse class contains a generalResponse object and the mPAYTID and/or tid of the transaction which was managed
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @filesource MPAY24SDK.php
 * @license MIT
 */
class ManagePaymentResponse extends GeneralResponse
{
    /**
     * An object, that represents the basic values from the response from mPAY24: status and return code
     *
     * @var string
     */
    var $generalResponse;

    /**
     * The mPAY transaction ID
     *
     * @var string
     */
    var $mpayTID;

    /**
     * The transaction ID of the shop
     *
     * @var string
     */
    var $tid;

    /**
     * Sets the values for a payment from the response from mPAY24: mPAY transaction IDand transaction ID from the shop
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    function __construct( $response ) {
        $this->generalResponse = new GeneralResponse($response);

        if ( '' != $response ) {
            $responseAsDOM = new DOMDocument();
            $responseAsDOM->loadXML($response);

            if ( $responseAsDOM && $responseAsDOM->getElementsByTagName('mpayTID')->length != 0 && $responseAsDOM->getElementsByTagName('tid')->length != 0 ) {
                $this->mpayTID = $responseAsDOM->getElementsByTagName('mpayTID')->item(0)->nodeValue;
                $this->tid = $responseAsDOM->getElementsByTagName('tid')->item(0)->nodeValue;
            }
        } else {
            $this->generalResponse->setStatus("ERROR");
            $this->generalResponse->setReturnCode("The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!");
        }
    }

    /**
     * Get the mPAY transaction ID, returned from mPAY24
     *
     * @return string
     */
    public function getMpayTID()
    {
        return $this->mpayTID;
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

    /**
     * Get the object, that contains the basic values from the response from mPAY24: status and return code
     *
     * @return string
     */
    public function getGeneralResponse()
    {
        return $this->generalResponse;
    }
}

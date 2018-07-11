<?php

namespace Mpay24\Responses;

/**
 * The AbstractPaymentResponse class contains all the parameters, returned with the confirmation from mPAY24
 *
 * Class AbstractPaymentResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource AbstractPaymentResponse.php
 * @license    MIT
 */
abstract class AbstractPaymentResponse extends AbstractResponse
{
    /**
     * @var int
     */
    protected $mpayTid;

    /**
     * @var int
     */
    protected $errNo;

    /**
     * @var string
     */
    protected $errText;

    /**
     * @var string
     */
    protected $location;

    /**
     * AbstractPaymentResponse constructor.
     *
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);
    }

    /**
     * @return int
     */
    public function getMpayTid()
    {
        return $this->mpayTid;
    }

    /**
     * @return int
     */
    public function getErrNo()
    {
        return $this->errNo;
    }

    /**
     * @return string
     */
    public function getErrText()
    {
        return $this->errText;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Parse the SelectPaymentResponse message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        if (!$body instanceof \DOMElement) {
            return;
        }
        if ($body->getElementsByTagName('mpayTID')->length > 0) {
            $this->mpayTid = (int)$body->getElementsByTagName('mpayTID')->item(0)->nodeValue;
        }

        if ($body->getElementsByTagName('errNo')->length > 0) {
            $this->errNo = (int)$body->getElementsByTagName('errNo')->item(0)->nodeValue;
        }

        if ($body->getElementsByTagName('errText')->length > 0) {
            $this->errText = $body->getElementsByTagName('errText')->item(0)->nodeValue;
        }

        if ($body->getElementsByTagName('location')->length > 0) {
            $this->location = $body->getElementsByTagName('location')->item(0)->nodeValue;
        }
    }
}

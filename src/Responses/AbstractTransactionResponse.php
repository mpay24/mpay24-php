<?php

namespace Mpay24\Responses;

/**
 * The GeneralTransactionResponse class contains the mPAYTID the basic information linked to it
 *
 * Class AbstractTransactionResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource AbstractTransactionResponse.php
 * @license    MIT
 */
abstract class AbstractTransactionResponse extends AbstractResponse
{
    /**
     * The mPAY24 transaction ID
     *
     * @var int
     */
    protected $mpayTid;

    /**
     * The mPAY24 transaction Status
     *
     * @var string
     */
    protected $tStatus;

    /**
     * The mPAY24 transaction ID
     *
     * @var int
     */
    protected $stateID;

    /**
     * The transaction ID of the shop
     *
     * @var string
     */
    protected $tid;

    /**
     * AbstractTransactionResponse constructor.
     *
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);
    }

    /**
     * Get the mPAY transaction ID, returned from mPAY24
     *
     * @return string
     */
    public function getMpayTid()
    {
        return $this->mpayTid;
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
     * @return string
     */
    public function getTStatus()
    {
        return $this->tStatus;
    }

    /**
     * @return int
     */
    public function getStateID()
    {
        return $this->stateID;
    }

    /**
     * Parse the Response message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        if ($this->responseAsDom->getElementsByTagName('mpayTID')->length > 0
            && $this->responseAsDom->getElementsByTagName('tid')->length > 0
        ) {
            $this->mpayTid = $this->responseAsDom->getElementsByTagName('mpayTID')->item(0)->nodeValue;
            $this->tid       = $this->responseAsDom->getElementsByTagName('tid')->item(0)->nodeValue;
        }
    }
}

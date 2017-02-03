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
    protected $stateId;

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
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Parse the Response message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        if ($body->getElementsByTagName('transaction')->length > 0) {
            $transaction = $body->getElementsByTagName('transaction')->item(0);

            $this->mpayTid = $transaction->getElementsByTagName('mpayTID')->item(0)->nodeValue;
            $this->tStatus = $transaction->getElementsByTagName('tStatus')->item(0)->nodeValue;
            $this->tid     = $transaction->getElementsByTagName('tid')->item(0)->nodeValue;

            if ($transaction->getElementsByTagName('stateID')->length > 0) {
                $this->stateId = $transaction->getElementsByTagName('stateID')->item(0)->nodeValue;
            }
        }
    }
}

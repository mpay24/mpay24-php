<?php

namespace Mpay24\Requests;

/**
 * The ManualCredit class create the body for te SOAP Requests
 *
 * Class ManualCredit
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ManualCredit.php
 * @license    MIT
 */
class ManualCredit extends AbstractRequest
{
    /**
     * @var int
     */
    protected $mpayTid;

    /**
     * @var int
     */
    protected $stateId;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @param int $mpayTid
     */
    public function setMpayTid($mpayTid)
    {
        $this->mpayTid = (int)$mpayTid;
    }

    /**
     * @param int $stateId
     */
    public function setStateId($stateId)
    {
        $this->stateId = (int)$stateId;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = (int)$amount;
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('ManualCredit');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $xmlMPayTid = $this->document->createElement('mpayTID', $this->mpayTid);
        $operation->appendChild($xmlMPayTid);

        if ($this->stateId) {
            $stateId = $this->document->createElement('stateID', $this->stateId);
            $operation->appendChild($stateId);
        }

        if ($this->amount) {
            $amount = $this->document->createElement('amount', $this->amount);
            $operation->appendChild($amount);
        }
    }
}

<?php

namespace Mpay24\Requests;

/**
 * The ManualClear class create the body for te SOAP Requests
 *
 * Class ManualClear
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ManualClear.php
 * @license    MIT
 */
class ManualClear extends AbstractRequest
{
    /**
     * @var int
     */
    protected $mpayTid;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var string
     */
    protected $order;

    /**
     * @param int $mpayTid
     */
    public function setMpayTid($mpayTid)
    {
        $this->mpayTid = (int)$mpayTid;
    }

    /**
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
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
        $operation = $this->buildOperation('ManualClear');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $clearingDetails = $this->document->createElement('clearingDetails');
        $clearingDetails = $operation->appendChild($clearingDetails);

        $xmlMPayTid = $this->document->createElement('mpayTID', $this->mpayTid);
        $clearingDetails->appendChild($xmlMPayTid);

        if ($this->amount) {
            $price = $this->document->createElement('amount', $this->amount);
            $clearingDetails->appendChild($price);
        }

        if ($this->order) {
            // TODO:: Add the order to the request
        }
    }
}

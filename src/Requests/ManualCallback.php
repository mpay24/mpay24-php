<?php

namespace Mpay24\Requests;

use InvalidArgumentException;

/**
 * The ManualCallback class create the body for te SOAP Requests
 *
 * Class ManualCallback
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ManualCallback.php
 * @license    MIT
 */
class ManualCallback extends AbstractRequest
{
    /**
     * @var int
     */
    protected $mpayTid;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var bool
     */
    protected $cancel;

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
     * @param string $type
     */
    public function setType($type)
    {
        if (preg_match('/^(PAYPAL|MASTERPASS)$/', $type) != 1) {
            throw new InvalidArgumentException();
        }
        $this->type = $type;
    }

    /**
     * @param bool $cancel
     */
    public function setCancel($cancel)
    {
        $this->cancel = (bool)$cancel;
    }

    /**
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @param string $amount
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
        $operation = $this->buildOperation('ManualCallback');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $xmlMPayTid = $this->document->createElement('mpayTID', $this->mpayTid);
        $operation->appendChild($xmlMPayTid);

        $paymentCallback = $this->document->createElement('paymentCallback');
        $paymentCallback->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance',
            'xsi:type', "etp:Callback $this->type");
        $paymentCallback = $operation->appendChild($paymentCallback);

        if ($this->amount) {
            $price = $this->document->createElement('amount', $this->amount);
            $paymentCallback->appendChild($price);
        }

        if ($this->cancel) {
            $cancel = $this->document->createElement('cancel', $this->cancel);
            $paymentCallback->appendChild($cancel);
        }

        if ($this->order) {
            // TODO:: Add the order to the request
        }
    }
}

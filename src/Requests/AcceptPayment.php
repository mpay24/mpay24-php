<?php

namespace Mpay24\Requests;

use InvalidArgumentException;

/**
 * The AcceptPayment class create the body for te SOAP Requests
 *
 * Class AcceptPayment
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource AcceptPayment.php
 * @license    MIT
 */
class AcceptPayment extends AbstractRequest
{
    /**
     * @var string
     */
    protected $tid;

    /**
     * @var int
     */
    protected $pType;

    /**
     * @var array
     */
    protected $payment;

    /**
     * @var array
     */
    protected $additional;

    /**
     * @param string $tid
     */
    public function setTid($tid)
    {
        $this->tid = $tid;
    }

    /**
     * @param string $pType
     */
    public function setPType($pType)
    {
        $this->pType = $pType;
    }

    /**
     * @param array $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @param array $additional
     */
    public function setAdditional($additional)
    {
        $this->additional = $additional;
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('AcceptPayment');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $tid = $this->document->createElement('tid', $this->tid);
        $operation->appendChild($tid);

        $pType = $this->document->createElement('pType', $this->pType);
        $operation->appendChild($pType);


        $payment = $this->document->createElement('payment');
        $payment->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'etp:Payment' . $this->pType);
        $payment = $operation->appendChild($payment);

        $this->appendArray($payment, $this->payment);

        $this->appendArray($operation, $this->additional);
    }
}

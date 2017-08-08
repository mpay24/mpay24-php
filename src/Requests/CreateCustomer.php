<?php

namespace Mpay24\Requests;

use InvalidArgumentException;

/**
 * The CreateCustomer class create the body for te SOAP Requests
 *
 * Class CreateCustomer
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Tobias Lins <tobias.lins@mpay24.com>
 * @filesource CreateCustomer.php
 * @license    MIT
 */
class CreateCustomer extends AbstractRequest
{
    /**
     * @var int
     */
    protected $pType;

    /**
     * @var array
     */
    protected $paymentData;

    /**
     * @var string
     */
    protected $customerId;

    /**
     * @var array
     */
    protected $additional;


    /**
     * @param string $pType
     */
    public function setPType($pType)
    {
        $this->pType = $pType;
    }

    /**
     * @param array $paymentData
     */
    public function setPaymentData($paymentData)
    {
        $this->paymentData = $paymentData;
    }

    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
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
        $operation = $this->buildOperation('CreateCustomer');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $pType = $this->document->createElement('pType', $this->pType);
        $operation->appendChild($pType);

        $paymentData = $this->document->createElement('paymentData');
        $paymentData->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'etp:PaymentData' . $this->pType);
        $paymentData = $operation->appendChild($paymentData);

        $this->appendArray($paymentData, $this->paymentData);

        $customerID = $this->document->createElement('customerID', $this->customerId);
        $operation->appendChild($customerID);
        $this->appendArray($operation, $this->additional);
    }
}

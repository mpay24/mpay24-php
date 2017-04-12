<?php

namespace Mpay24\Requests;

/**
 * The TransactionHistory class create the body for te SOAP Requests
 *
 * Class TransactionHistory
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource TransactionHistory.php
 * @license    MIT
 */
class TransactionHistory extends AbstractRequest
{
    /**
     * @var int
     */
    protected $mpayTid;

    /**
     * @param string $mpayTid
     */
    public function setMpayTid($mpayTid)
    {
        $this->mpayTid = (int)$mpayTid;
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('TransactionHistory');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $xmlMPayTid = $this->document->createElement('mpayTID', $this->mpayTid);
        $operation->appendChild($xmlMPayTid);
    }
}

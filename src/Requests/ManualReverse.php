<?php

namespace Mpay24\Requests;

/**
 * The ManualReverse class create the body for te SOAP Requests
 *
 * Class ManualReverse
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ManualReverse.php
 * @license    MIT
 */
class ManualReverse extends AbstractRequest
{
    /**
     * @var int
     */
    protected $mpayTid;

    /**
     * @param int $mpayTid
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
        $operation = $this->buildOperation('ManualReverse');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $xmlMPayTid = $this->document->createElement('mpayTID', $this->mpayTid);
        $operation->appendChild($xmlMPayTid);
    }
}

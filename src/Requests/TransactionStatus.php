<?php

namespace Mpay24\Requests;

/**
 * The TransactionStatus class create the body for te SOAP Requests
 *
 * Class TransactionStatus
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource TransactionStatus.php
 * @license    MIT
 */
class TransactionStatus extends AbstractRequest
{
    /**
     * @var int
     */
    protected $mpayTid;

    /**
     * @var string
     */
    protected $tid;

    /**
     * @param int $mpayTid
     */
    public function setMpayTid($mpayTid)
    {
        $this->mpayTid = (int)$mpayTid;
    }

    /**
     * @param string $tid
     */
    public function setTid($tid)
    {
        $this->tid = $tid;
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('TransactionStatus');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        if ($this->mpayTid) {
            $xmlMPayTid = $this->document->createElement('mpayTID', $this->mpayTid);
            $operation->appendChild($xmlMPayTid);
        }

        if ($this->tid) {
            $xmlTid = $this->document->createElement('tid', $this->tid);
            $operation->appendChild($xmlTid);
        }
    }
}

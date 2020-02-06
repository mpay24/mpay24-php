<?php

namespace Mpay24\Requests;

/**
 * The ListProfiles class create the body for te SOAP Requests
 *
 * Class ListProfiles
 * @package    Mpay24\Request
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource ListProfiles.php
 * @license    MIT
 */
class ListProfiles extends AbstractRequest
{
    /**
     * @var string
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $expiredBy;

    /**
     * @var integer
     */
    protected $begin;

    /**
     * @var integer
     */
    protected $size;

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @param string $expiredBy
     */
    public function setExpiredBy($expiredBy)
    {
        // TODO: check format
        $this->expiredBy = $expiredBy;
    }

    /**
     * @param integer $begin
     */
    public function setBegin($begin)
    {
        $this->begin = (int)$begin;
    }

    /**
     * @param integer $size
     */
    public function setSize($size)
    {
        $this->size = (int)$size;
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('ListProfiles');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        if ($this->customerId) {
            $xmlMPayTid = $this->document->createElement('customerID', $this->customerId);
            $operation->appendChild($xmlMPayTid);
        }

        if ($this->expiredBy) {
            $xmlMPayTid = $this->document->createElement('expiredBy', $this->expiredBy);
            $operation->appendChild($xmlMPayTid);
        }

        if ($this->begin) {
            $xmlMPayTid = $this->document->createElement('begin', $this->begin);
            $operation->appendChild($xmlMPayTid);
        }

        if ($this->size) {
            $xmlMPayTid = $this->document->createElement('size', $this->size);
            $operation->appendChild($xmlMPayTid);
        }
    }
}

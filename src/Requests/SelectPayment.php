<?php

namespace Mpay24\Requests;

/**
 * The SelectPayment class create the body for te SOAP Requests
 *
 * Class SelectPayment
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource SelectPayment.php
 * @license    MIT
 */
class SelectPayment extends AbstractRequest
{
    /**
     * @var int
     */
    protected $mdxi;

    /**
     * @param string $mdxi
     */
    public function setMdxi($mdxi)
    {
        $this->mdxi = htmlspecialchars($mdxi);
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('SelectPayment');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $xmlMdxi = $this->document->createElement('mdxi', $this->mdxi);
        $operation->appendChild($xmlMdxi);
    }
}

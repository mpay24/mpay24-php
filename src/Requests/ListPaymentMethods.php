<?php

namespace Mpay24\Requests;

use InvalidArgumentException;

/**
 * The ListPaymentMethods class create the body for te SOAP Requests
 *
 * Class ListPaymentMethods
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ListPaymentMethods.php
 * @license    MIT
 */
class ListPaymentMethods extends AbstractRequest
{
    /**
     * @var int
     */
    protected $pType;

    /**
     * @param string $pType
     */
    public function setPType($pType)
    {
        $this->pType = $pType;
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('ListPaymentMethods');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        if ($this->pType) {
            $pType = $this->document->createElement('pType', $this->pType);
            $operation->appendChild($pType);
        }
    }
}

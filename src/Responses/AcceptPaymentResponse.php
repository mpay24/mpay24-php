<?php

namespace Mpay24\Responses;

/**
 * The AcceptPaymentResponse class contains all the parameters, returned with the confirmation from mPAY24
 *
 * Class AcceptPaymentResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource AcceptPaymentResponse.php
 * @license    MIT
 */
class AcceptPaymentResponse extends AbstractPaymentResponse
{
    /**
     * AcceptPaymentResponse constructor.
     *      The SOAP response from mPAY24 (in XML form)
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);
        $this->parseResponse($this->getBody('AcceptPaymentResponse'));
    }
}

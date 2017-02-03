<?php

namespace Mpay24\Responses;

/**
 * The ManualCallbackResponse class contains all the parameters, returned with the confirmation from mPAY24
 *
 * Class ManualCallbackResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ManualCallbackResponse.php
 * @license    MIT
 */
class ManualCallbackResponse extends AbstractPaymentResponse
{
    /**
     * ManualCallbackResponse constructor.
     *      The SOAP response from mPAY24 (in XML form)
     *
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            $this->parseResponse($this->getBody('ManualCallbackResponse'));
        }
    }
}

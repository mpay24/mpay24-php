<?php

namespace Mpay24\Responses;

/**
 * The CreateCustomerResponse class contains all the parameters, returned with the confirmation from mPAY24
 *
 * Class CreateCustomerResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Tobias Lins <tobias.lins@mpay24.com>
 * @filesource CreateCustomerResponse.php
 * @license    MIT
 */
class CreateCustomerResponse extends AbstractResponse
{
    /**
     * @var int
     */
    protected $errNo;

    /**
     * CreateCustomerResponse constructor.
     *      The SOAP response from mPAY24 (in XML form)
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoException()) {

            $this->parseResponse($this->getBody('CreateCustomerResponse'));
        }
    }

    /**
     * Parse the CreateCustomerResponse message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        if ($body->getElementsByTagName('errNo')->length > 0) {
            $this->errNo = (int)$body->getElementsByTagName('errNo')->item(0)->nodeValue;
        }
    }
}

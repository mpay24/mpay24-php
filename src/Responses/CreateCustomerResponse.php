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
     * @var string
     */
    protected $errText;

    /**
     * @var string
     */
    protected $location;

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
     * @return int
     */
    public function getErrNo()
    {
        return $this->errNo;
    }
	
	/**
     * @return string
     */
    public function getErrText()
    {
        return $this->errText;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Parse the CreateCustomerResponse message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        $this->location = $body->getElementsByTagName('location')->item(0)->nodeValue;


        if ($body->getElementsByTagName('errNo')->length > 0) {
            $this->errNo = (int)$body->getElementsByTagName('errNo')->item(0)->nodeValue;
        }

        if ($body->getElementsByTagName('errText')->length > 0) {
            $this->errText = $body->getElementsByTagName('errText')->item(0)->nodeValue;
        }
    }
}

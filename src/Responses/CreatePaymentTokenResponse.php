<?php

namespace Mpay24\Responses;

/**
 * The CreatePaymentTokenResponse class contains all the parameters, returned with the confirmation from mPAY24
 *
 * Class CreatePaymentTokenResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource CreatePaymentTokenResponse.php
 * @license    MIT
 */
class CreatePaymentTokenResponse extends AbstractResponse
{
    /**
     * The token, got back from Mpay24, which will be used for the actual payment
     *
     * @var string
     */
    protected $token;

    /**
     * The api key, got back from Mpay24, which will be used for the actual payment
     *
     * @var int
     */
    protected $apiKey;

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
     * CreatePaymentTokenResponse constructor.
     *
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            $this->parseResponse($this->getBody('CreatePaymentTokenResponse'));
        }
    }

    /**
     * Get the token, returned from mPAY24
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getApiKey()
    {
        return $this->apiKey;
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
     * Parse the SelectPaymentResponse message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        if ($body->getElementsByTagName('token')->length > 0) {
            $this->token = $body->getElementsByTagName('token')->item(0)->nodeValue;
        }

        if ($body->getElementsByTagName('apiKey')->length > 0) {
            $this->apiKey = $body->getElementsByTagName('apiKey')->item(0)->nodeValue;
        }

        if ($body->getElementsByTagName('errNo')->length > 0) {
            $this->errNo = (int)$body->getElementsByTagName('errNo')->item(0)->nodeValue;
        }

        if ($body->getElementsByTagName('errText')->length > 0) {
            $this->errText = $body->getElementsByTagName('errText')->item(0)->nodeValue;
        }

        if ($body->getElementsByTagName('location')->length > 0) {
            $this->location = $body->getElementsByTagName('location')->item(0)->nodeValue;
        }
    }
}

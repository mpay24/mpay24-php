<?php

namespace Mpay24\Responses;

/**
 * The CreateGooglePayTokenResponse class contains all the parameters, returned with the confirmation from mPAY24
 *
 * Class CreateGooglePayTokenResponse
 * @package    Mpay24\Responses
 *
 * @author     Unzer Austria GmbH <online.support.at@unzer.com>
 * @author     Stefan Polzer <develop@ps-webdesign.com>, Milko Daskalov <milko.daskalov@unzer.com>
 * @filesource CreateGooglePayTokenResponse.php
 * @license    MIT
 */
class CreateGooglePayTokenResponse extends AbstractResponse
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
     * CreateGooglePayTokenResponse constructor.
     *
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoException()) {

            $this->parseResponse($this->getBody('CreateGooglePayTokenResponse'));
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
    }
}

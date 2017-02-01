<?php

namespace Mpay24\Responses;

/**
 * Class PaymentTokenResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource PaymentTokenResponse.php
 * @license    MIT
 */
class PaymentTokenResponse extends PaymentResponse
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
     * @var string
     */
    protected $apiKey;

    /**
     * Sets the values for a payment from the response from mPAY24: mPAY transaction ID, error number, location (URL), token and apiKey
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            if ($this->responseAsDom->getElementsByTagName('token')->length != 0) {
                $this->token = $this->responseAsDom->getElementsByTagName('token')->item(0)->nodeValue;
            }

            if ($this->responseAsDom->getElementsByTagName('apiKey')->length != 0) {
                $this->apiKey = $this->responseAsDom->getElementsByTagName('apiKey')->item(0)->nodeValue;
            }
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
     * Get the api key, returned from mPAY24
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}

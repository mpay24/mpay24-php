<?php

namespace MPayAPI\core;

class PaymentTokenResponse extends PaymentResponse {
  /**
   * An object, that represents the basic payment values from the response from mPAY24: status, return code and location
   *
   * @var string
   */
  public $paymentResponse;
  /**
   * The token, got back from mPAY24, which will be used for the actual payment
   *
   * @var string
   */
  public $token;
  
  /**
   * The api key, got back from mPAY24, which will be used for the actual payment
   *
   * @var string
   */
  public $apiKey;

  /**
   * Sets the values for a payment from the response from mPAY24: mPAY transaction ID, error number, location (URL), token and apiKey
   *
   * @param string $response
   *          The SOAP response from mPAY24 (in XML form)
   */
  public function __construct($response) {
    $this->paymentResponse = new PaymentResponse($response);

    if($response != '') {
      $responseAsDOM = new DOMDocument();
      $responseAsDOM->loadXML($response);

      if(! empty($responseAsDOM) && is_object($responseAsDOM) && $responseAsDOM->getElementsByTagName('token')->length != 0)
        $this->token = $responseAsDOM->getElementsByTagName('token')->item(0)->nodeValue;
      if(! empty($responseAsDOM) && is_object($responseAsDOM) && $responseAsDOM->getElementsByTagName('apiKey')->length != 0)
        $this->apiKey = $responseAsDOM->getElementsByTagName('apiKey')->item(0)->nodeValue;
    } else {
      $this->paymentResponse->generalResponse->setStatus("ERROR");
      $this->paymentResponse->generalResponse->setReturnCode("The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!");
    }
  }

  /**
   * Get the token, returned from mPAY24
   *
   * @return string
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * Get the api key, returned from mPAY24
   *
   * @return string
   */
  public function getApiKey() {
    return $this->apiKey;
  }
  
  /**
   * Get the object, that contains the basic payment values from the response from mPAY24: status, return code and location
   *
   * @return string
   */
  public function getPaymentResponse() {
    return $this->paymentResponse;
  }
}

?>

<?php

namespace MPayAPI\core;

/**
 * The PaymentResponse class contains a generalResponse object and the location(URL), which will be used for the payment session
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @version $Id: MPay24Api.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource MPay24Api.php
 * @license http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class PaymentResponse extends GeneralResponse {
  /**
   * An object, that represents the basic values from the response from mPAY24: status and return code
   *
   * @var string
   */
  var $generalResponse;
  /**
   * An URL (of the mPAY24 payment fenster), where the customer would be redirected to, in case of successfull request
   *
   * @var string
   */
  var $location;
  /**
   * The unique ID returned by mPAY24 for every transaction
   *
   * @var string
   */
  var $mpayTID;
  
  /**
   * Sets the values for a payment from the response from mPAY24: mPAY transaction ID, error number and location (URL)
   *
   * @param string $response
   *          The SOAP response from mPAY24 (in XML form)
   */
  function PaymentResponse($response) {
    $this->generalResponse = new GeneralResponse($response);
    
    if($response != '') {
      $responseAsDOM = new DOMDocument();
      $responseAsDOM->loadXML($response);
      
      if(! empty($responseAsDOM) && is_object($responseAsDOM) && $responseAsDOM->getElementsByTagName('location')->length != 0)
        $this->location = $responseAsDOM->getElementsByTagName('location')->item(0)->nodeValue;
      if(! empty($responseAsDOM) && is_object($responseAsDOM) && $responseAsDOM->getElementsByTagName('mpayTID')->length != 0)
        $this->mpayTID = $responseAsDOM->getElementsByTagName('mpayTID')->item(0)->nodeValue;
    } else {
      $this->generalResponse->setStatus("ERROR");
      $this->generalResponse->setReturnCode("The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!");
    }
  }
  
  /**
   * Get the location (URL), returned from mPAY24
   *
   * @return string
   */
  public function getLocation() {
    return $this->location;
  }
  
  /**
   * Get the unique ID, returned from mPAY24
   *
   * @return string
   */
  public function getMpayTID() {
    return $this->mpayTID;
  }
  
  /**
   * Get the object, that contains the basic values from the response from mPAY24: status and return code
   *
   * @return string
   */
  public function getGeneralResponse() {
    return $this->generalResponse;
  }
}

?>

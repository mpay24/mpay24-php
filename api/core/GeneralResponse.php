<?php

namespace MPayAPI\core;

/**
 * The GeneralResponse class contains the status of a response and return code, which was delivered by mPAY24 as an answer of your request
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @version $Id: MPay24Api.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource MPay24Api.php
 * @license http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class GeneralResponse {
  /**
   * The status of the request, which was sent to mPAY24
   *
   * @var string
   */
  var $status;
  /**
   * The return code from the request, which was sent to mPAY24
   *
   * @var string
   */
  var $returnCode;
  
  /**
   * Sets the basic values from the response from mPAY24: status and return code
   *
   * @param string $response
   *          The SOAP response from mPAY24 (in XML form)
   */
  function GeneralResponse($response) {
    if($response != '') {
      $responseAsDOM = new DOMDocument();
      $responseAsDOM->loadXML($response);
      
      if(! empty($responseAsDOM) && is_object($responseAsDOM))
        if(! $responseAsDOM || $responseAsDOM->getElementsByTagName('status')->length == 0 || $responseAsDOM->getElementsByTagName('returnCode')->length == 0) {
          $this->status = "ERROR";
          $this->returnCode = urldecode($response);
        } else {
          $this->status = $responseAsDOM->getElementsByTagName('status')->item(0)->nodeValue;
          $this->returnCode = $responseAsDOM->getElementsByTagName('returnCode')->item(0)->nodeValue;
        }
    } else {
      $this->status = "ERROR";
      $this->returnCode = "The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!";
    }
  }
  
  /**
   * Get the status of the request, which was sent to mPAY24
   *
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }
  
  /**
   * Get the return code from the request, which was sent to mPAY24
   *
   * @return string
   */
  public function getReturnCode() {
    return $this->returnCode;
  }
  
  /**
   * Set the status in the response, which was delivered by mPAY24
   *
   * @param string $status
   *          Status
   */
  public function setStatus($status) {
    $this->status = $status;
  }
  
  /**
   * Set the return code in the response, which was delivered by mPAY24
   *
   * @param string $returnCode
   *          Return code
   */
  public function setReturnCode($returnCode) {
    return $this->returnCode = $returnCode;
  }
}

?>

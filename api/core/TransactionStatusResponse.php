<?php

namespace MPayAPI\core;

/**
 * The TransactionStatusResponse class contains a generalResponse object and all the parameters, returned with the confirmation from mPAY24
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @version $Id: MPay24Api.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource MPay24Api.php
 * @license http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class TransactionStatusResponse extends GeneralResponse {
  /**
   * An object, that represents the basic values from the response from mPAY24: status and return code
   *
   * @var string
   */
  var $generalResponse;
  /**
   * A list with all the parameters for a transaction
   *
   * @var array
   */
  var $params = array();
  /**
   * The count of all the paramerters for a transaction
   *
   * @var int
   */
  var $paramCount = 0;
  
  /**
   * Sets the values for a transaction from the response from mPAY24: STATUS, PRICE, CURRENCY, LANGUAGE, etc
   *
   * @param string $response
   *          The SOAP response from mPAY24 (in XML form)
   */
  function TransactionStatusResponse($response) {
    $this->generalResponse = new GeneralResponse($response);
    
    if($response != '') {
      $responseAsDOM = new DOMDocument();
      $responseAsDOM->loadXML($response);
      
      if($responseAsDOM && $responseAsDOM->getElementsByTagName('name')->length != 0) {
        $this->paramCount = $responseAsDOM->getElementsByTagName('name')->length;
        $this->params['STATUS'] = $this->generalResponse->getStatus();
        
        for($i = 0; $i < $this->paramCount; $i ++) {
          if($responseAsDOM->getElementsByTagName("name")->item($i)->nodeValue == "STATUS")
            $this->params["TSTATUS"] = $responseAsDOM->getElementsByTagName("value")->item($i)->nodeValue;
          else
            $this->params[$responseAsDOM->getElementsByTagName('name')->item($i)->nodeValue] = $responseAsDOM->getElementsByTagName('value')->item($i)->nodeValue;
        }
      }
    } else {
      $this->generalResponse->setStatus("ERROR");
      $this->generalResponse->setReturnCode("The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!");
    }
  }
  
  /**
   * Get the count of all the paramerters for a transaction
   *
   * @return int
   */
  public function getParamCount() {
    return $this->paramCount;
  }
  
  /**
   * Get the parameters for a transaction, returned from mPAY24
   *
   * @return array
   */
  public function getParams() {
    return $this->params;
  }
  
  /**
   * Get the parameter's value, returned from mPAY24
   *
   * @param string $i
   *          The name of a parameter (for example: STATUS, PRICE, CURRENCY, etc)
   * @return array
   */
  public function getParam($i) {
    if(isset($this->params[$i]))
      return $this->params[$i];
    else
      return false;
  }
  
  /**
   * Set a value for a parameter
   *
   * @param string $name
   *          The name of a parameter (for example: STATUS, PRICE, CURRENCY, etc)
   * @param string $value
   *          The value of the parameter
   */
  public function setParam($name, $value) {
    $this->params[$name] = $value;
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

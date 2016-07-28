<?php

namespace MPayAPI\core;

class PaymentTokenResponse extends PaymentResponse {
  /**
   * An object, that represents the basic payment values from the response from mPAY24: status, return code and location
   *
   * @var string
   */
  var $paymentResponse;
  /**
   * The token, got back from mPAY24, which will be used for the actual payment
   *
   * @var string
   */
  var $token;
  
  /**
   * The api key, got back from mPAY24, which will be used for the actual payment
   *
   * @var string
   */
  var $apiKey;

  /**
   * Sets the values for a payment from the response from mPAY24: mPAY transaction ID, error number, location (URL), token and apiKey
   *
   * @param string $response
   *          The SOAP response from mPAY24 (in XML form)
   */
  function PaymentTokenResponse($response) {
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

/**
 * The ManagePaymentResponse class contains a generalResponse object and the mPAYTID and/or tid of the transaction which was managed
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @version $Id: MPay24Api.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource MPay24Api.php
 * @license http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class ManagePaymentResponse extends GeneralResponse {
  /**
   * An object, that represents the basic values from the response from mPAY24: status and return code
   *
   * @var string
   */
  var $generalResponse;
  /**
   * The mPAY transaction ID
   *
   * @var string
   */
  var $mpayTID;
  /**
   * The transaction ID of the shop
   *
   * @var string
   */
  var $tid;
  
  /**
   * Sets the values for a payment from the response from mPAY24: mPAY transaction IDand transaction ID from the shop
   *
   * @param string $response
   *          The SOAP response from mPAY24 (in XML form)
   */
  function ManagePaymentResponse($response) {
    $this->generalResponse = new GeneralResponse($response);
    
    if($response != '') {
      $responseAsDOM = new DOMDocument();
      $responseAsDOM->loadXML($response);
      
      if($responseAsDOM && $responseAsDOM->getElementsByTagName('mpayTID')->length != 0 && $responseAsDOM->getElementsByTagName('tid')->length != 0) {
        $this->mpayTID = $responseAsDOM->getElementsByTagName('mpayTID')->item(0)->nodeValue;
        $this->tid = $responseAsDOM->getElementsByTagName('tid')->item(0)->nodeValue;
      }
    } else {
      $this->generalResponse->setStatus("ERROR");
      $this->generalResponse->setReturnCode("The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!");
    }
  }
  
  /**
   * Get the mPAY transaction ID, returned from mPAY24
   *
   * @return string
   */
  public function getMpayTID() {
    return $this->mpayTID;
  }
  
  /**
   * Get the transaction ID of the shop, returned from mPAY24
   *
   * @return string
   */
  public function getTid() {
    return $this->tid;
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

/**
 * The ListPaymentMethodsResponse class contains a generalResponse object and all the needed informarion for the active payment mothods (payment methods count, payment types, brands and descriptions)
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @version $Id: MPay24Api.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource MPay24Api.php
 * @license http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class ListPaymentMethodsResponse extends GeneralResponse {
  /**
   * An object, that represents the basic values from the response from mPAY24: status and return code
   *
   * @var string
   */
  var $generalResponse;
  /**
   * The count of the payment methods, which are activated by mPAY24
   *
   * @var int
   */
  var $all = 0;
  /**
   * A list with the payment types, activated by mPAY24
   *
   * @var array
   */
  var $pTypes = array();
  /**
   * A list with the brands, activated by mPAY24
   *
   * @var array
   */
  var $brands = array();
  /**
   * A list with the descriptions of the payment methods, activated by mPAY24
   *
   * @var array
   */
  var $descriptions = array();
  
  /**
   * A list with the IDs of the payment methods, activated by mPAY24
   *
   * @var array
   */
  var $pMethIds = array();
  
  /**
   * Sets the values for a payment from the response from mPAY24: count, payment types, brands and descriptions
   *
   * @param string $response
   *          The SOAP response from mPAY24 (in XML form)
   */
  function ListPaymentMethodsResponse($response) {
    $this->generalResponse = new GeneralResponse($response);
    
    if($response != '') {
      $responseAsDOM = new DOMDocument();
      $responseAsDOM->loadXML($response);
      
      if($responseAsDOM && $responseAsDOM->getElementsByTagName('all')->length != 0) {
        $this->all = $responseAsDOM->getElementsByTagName('all')->item(0)->nodeValue;
        
        for($i = 0; $i < $this->all; $i ++) {
          $this->pTypes[$i] = $responseAsDOM->getElementsByTagName('pType')->item($i)->nodeValue;
          $this->brands[$i] = $responseAsDOM->getElementsByTagName('brand')->item($i)->nodeValue;
          $this->descriptions[$i] = $responseAsDOM->getElementsByTagName('description')->item($i)->nodeValue;
          $this->pMethIds[$i] = $responseAsDOM->getElementsByTagName('paymentMethod')->item($i)->getAttribute("id");
        }
      }
    } else {
      $this->generalResponse->setStatus("ERROR");
      $this->generalResponse->setReturnCode("The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!");
    }
  }
  
  /**
   * Get the count of the payment methods, returned from mPAY24
   *
   * @return int
   */
  public function getAll() {
    return $this->all;
  }
  
  /**
   * Get the payment types, returned from mPAY24
   *
   * @return array
   */
  public function getPTypes() {
    return $this->pTypes;
  }
  
  /**
   * Get the brands, returned from mPAY24
   *
   * @return array
   */
  public function getBrands() {
    return $this->brands;
  }
  
  /**
   * Get the descriptions, returned from mPAY24
   *
   * @return array
   */
  public function getDescriptions() {
    return $this->descriptions;
  }
  
  /**
   * Get the payment method IDs, returned from mPAY24
   *
   * @return array
   */
  public function getPMethIDs() {
    return $this->pMethIds;
  }
  
  /**
   * Get payment type, returned from mPAY24
   *
   * @param int $i
   *          The index of a payment type
   * @return string
   */
  public function getPType($i) {
    return $this->pTypes[$i];
  }
  
  /**
   * Get brand, returned from mPAY24
   *
   * @param int $i
   *          The index of a brand
   * @return string
   */
  public function getBrand($i) {
    return $this->brands[$i];
  }
  
  /**
   * Get description, returned from mPAY24
   *
   * @param int $i
   *          The index of a description
   * @return string
   */
  public function getDescription($i) {
    return $this->descriptions[$i];
  }
  
  /**
   * Get payment method ID, returned from mPAY24
   *
   * @param int $i
   *          The index of an payment method ID
   * @return int
   */
  public function getPMethID($i) {
    return $this->pMethIds[$i];
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

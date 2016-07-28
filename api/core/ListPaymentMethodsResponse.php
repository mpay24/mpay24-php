<?php

namespace MPayAPI\core;

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
  public $generalResponse;
  /**
   * The count of the payment methods, which are activated by mPAY24
   *
   * @var int
   */
  public $all = 0;
  /**
   * A list with the payment types, activated by mPAY24
   *
   * @var array
   */
  public $pTypes = array();
  /**
   * A list with the brands, activated by mPAY24
   *
   * @var array
   */
  public $brands = array();
  /**
   * A list with the descriptions of the payment methods, activated by mPAY24
   *
   * @var array
   */
  public $descriptions = array();
  
  /**
   * A list with the IDs of the payment methods, activated by mPAY24
   *
   * @var array
   */
  public $pMethIds = array();
  
  /**
   * Sets the values for a payment from the response from mPAY24: count, payment types, brands and descriptions
   *
   * @param string $response
   *          The SOAP response from mPAY24 (in XML form)
   */
  public function __construct($response) {
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

?>

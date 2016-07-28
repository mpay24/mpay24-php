<?php

namespace MPayAPI\core;

/**
 * The properties, which are allowed for a transaction
 * @const TRANSACTION_PROPERTIES
 */
define("TRANSACTION_PROPERTIES", "SECRET,TID,STATUS,MPAYTID,APPR_CODE,P_TYPE,
                                  BRAND,PRICE,CURRENCY,OPERATION,LANGUAGE,
                                  USER_FIELD,ORDERDESC,CUSTOMER,CUSTOMER_EMAIL,
                                  CUSTOMER_ID,PROFILE_STATUS,FILTER_STATUS,TSTATUS");

/**
 * The Transaction class allows you to set and get different trnasaction's properties - see details
 *
 * TYPE: PARAMETER - VALUE(s), description
 *
 * * STRING: STATUS - OK, ERROR
 * * STRING: OPERATION - CONFIRMATION
 * * STRING: TID - length <= 32
 * * STRING: TRANSACTION_STATUS - RESERVED, BILLED, REVERSED, CREDITED, ERROR
 * * INT: PRICE - length = 11 (e. g. "10" = "0,10")
 * * STRING: CURRENCY - length = 3 (ISO currency code, e. g. "EUR")
 * * STRING: P_TYPE - CC, ELV, EPS, GIROPAY, MAESTRO, PB, PSC, QUICK, etc
 * * STRING: BRAND - AMEX, DINERS, JCB, MASTERCARD, VISA, ATOS, HOBEX-AT, HOBEX-DE, etc
 * * INT: MPAYTID - length = 11
 * * STRING: USER_FIELD
 * * STRING: ORDERDESC
 * * STRING: CUSTOMER
 * * STRING: CUSTOMER_EMAIL
 * * STRING: LANGUAGE - length = 2
 * * STRING: CUSTOMER_ID - length = 11
 * * STRING: PROFILE_STATUS - IGNORED, USED, ERROR, CREATED, UPDATED, DELETED
 * * STRING: FILTER_STATUS
 * * STRING: APPR_CODE
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @version $Id: MPay24Shop.php 6271 2015-04-09 08:38:50Z anna $
 * @filesource MPay24Shop.php
 * @license http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class Transaction {
  /**
   * An array, which contains the allowed properties for an transaction
   *
   * @var $allowedProperties
   */
  public $allowedProperties = array();
  /**
   * An array, which contains the set properties for this transaction object
   *
   * @var $allowedProperties
   */
  public $properties = array();
  
  /**
   * Create a transaction object and set the allowed properties from the TRANSACTION_PROPERTIES
   *
   * @param string $tid
   *          The ID of a transaction
   */
  public function Transaction($tid) {
    $this->allowedProperties = explode(",", preg_replace('/\s*/m', '', TRANSACTION_PROPERTIES));
    $this->TID = $tid;
  }
  
  /**
   * Get the property of the Transaction object
   *
   * @param string $property
   *          The name of the property, which is searched
   * @return string|bool
   */
  public function __get($property) {
    if(! in_array($property, $this->allowedProperties))
      die("The transaction's property " . $property . ", you want to get is not defined!");
    
    if(isset($this->properties[$property]))
      return $this->properties[$property];
    else
      return false;
  }
  
  /**
   * Set the property of the Transaction object
   *
   * @param string $property
   *          The name of the property you want to set, see TRANSACTION_PROPERTIES
   * @param mixed $value
   *          The value of the property you want to set
   */
  public function __set($property, $value) {
    if(! in_array($property, $this->allowedProperties))
      die("The transaction's property " . $property . ", you want to set is not defined!");
    $this->properties[$property] = $value;
  }
  
  /**
   * Set all the allowed properties for this transaction
   *
   * @param array $args
   *          An array with the allowed properties
   */
  protected function setProperties($args) {
    $this->properties = $args;
  }
  
  /**
   * Get all the allowed properties for this transaction
   *
   * @return array
   */
  protected function getProperties() {
    return $this->properties;
  }
}

?>

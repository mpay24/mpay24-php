<?php
/**
 * Main mPAY24 PHP APIs Class.
 *
 * The MPay24Api class provides the communication functioanallity. It hold's all the sensitive data (merchant ID, SOAP password, etc) and
 * build the SOAP request, sent to mPAY24.
 *
 * @author              mPAY24 GmbH <support@mpay24.com>
 * @version             $Id: MPay24Api.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource          MPay24Api.php
 * @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class MPay24Api {
  /**
   * TRUE, when you want to use the test system, and FALSE otherwise
   *
   * @var bool
   */
  private $test = false;
  /**
   * 'test', when you want to use the test system, and 'www' otherwise
   *
   * @var string
   */
  private $flexLINKSystem = "test";
  /**
   * The link where the requests should be sent to
   *
   * DEFAULT : https://test.mpay24.com/app/bin/etpproxy_v15 (TEST SYSTEM)
   *
   * @var string
   */
  private $etp_url = "https://test.mpay24.com/app/bin/etpproxy_v15";
  /**
   * The merchant ID (supported from mPAY24).
   * 5-digit number. Begin with 9
   * for test system, begin with 7 for the live system.
   *
   * @var int
   */
  private $merchantid = "9xxxx";
  /**
   * SPID (supported from mPAY24).
   *
   * @var string
   */
  private $spid = "";
  /**
   * The SOAP password (supproted from mPAY24)
   *
   * @var string
   */
  private $soappass = "";
  /**
   * The flexLINK password (supproted from mPAY24)
   *
   * @var string
   */
  private $pass = "";
  /**
   * The fix (envelope) part of the soap xml, which is to be sent to mPAY24
   *
   * @var string
   */
  private $soap_xml = "";
  /**
   * The host name, in case you are using proxy
   *
   * @var string
   */
  private $proxy_host = "";
  /**
   * 4-digit port number, in case you are using proxy
   *
   * @var int
   */
  private $proxy_port = "";
  /**
   * The user name, in case you are using proxy
   *
   * @var string
   */
  private $proxy_user = "";
  /**
   * The password, in case you are using proxy
   *
   * @var string
   */
  private $proxy_pass = "";
  /**
   * The whole soap-xml (envelope and body), which is to be sent to mPAY24 as request
   *
   * @var string
   */
  private $request = "";
  /**
   * The response from mPAY24
   *
   * @var string
   */
  private $response = "";
  /**
   * FALSE to stop cURL from verifying the peer's certificate, default - TRUE
   *
   * @var bool
   */
  private $verify_peer = true;
  /**
   * TRUE if log files are to be written, by default - FALSE
   *
   * @var bool
   */
  private $debug = true;
  /**
   * The name of the shopsoftware
   *
   * @var string
   */
  public $shop = "mPAY24 GmbH";
  /**
   * The veriosn of the shopsoftware
   *
   * @var string
   */
  public $shopVersion = "PHP APIs";
  /**
   * The version of the shop module
   *
   * @var string
   */
  public $moduleVersion = '$Rev: 6231 $ ($Date:: 2015-03-13 #$)';
  
  /**
   * Set the basic (mandatory) settings for the requests
   *
   * @param int $merchantID
   *          5-digit account number, supported by mPAY24
   *          
   *          TEST accounts - starting with 9
   *          
   *          LIVE account - starting with 7
   * @param string $soapPassword
   *          The webservice's password, supported by mPAY24
   * @param bool $test
   *          TRUE - when you want to use the TEST system
   *          
   *          FALSE - when you want to use the LIVE system
   * @param string $proxyHost
   *          The host name in case you are behind a proxy server ("" when not)
   * @param int $proxyPort
   *          4-digit port number in case you are behind a proxy server ("" when not)
   * @param string $proxyUser
   *          The proxy user in case you are behind a proxy server ("" when not)
   * @param string $proxyPass
   *          The proxy password in case you are behind a proxy server ("" when not)
   * @param bool $verifyPeer
   *          Set as FALSE to stop cURL from verifying the peer's certificate
   */
  public function configure($merchantID, $soapPassword, $test, $proxyHost, $proxyPort, $proxyUser, $proxyPass, $verifyPeer) {
    /**
     * An error message, that will be displayed to the user in case you are using the LIVE system
     * @const LIVE_ERROR_MSG
     */
    define('LIVE_ERROR_MSG', "We are sorry, an error occured - please contact the merchant!");
    
    /**
     * The current directory, where the script is runnig from
     * @const __DIR__
     */
    if(! defined('__DIR__'))
      define('__DIR__', dirname(__FILE__));
    
    $this->setMerchantID($merchantID);
    $this->setSoapPassword($soapPassword);
    $this->setSystem($test);
    
    if($proxyHost != "" && $proxyPort != "") {
      if($proxyUser != "" && $proxyPass != "")
        $this->setProxySettings($proxyHost, $proxyPort, $proxyUser, $proxyPass);
      else
        $this->setProxySettings($proxyHost, $proxyPort);
    }
    
    $this->setVerifyPeer($verifyPeer);
  }
  
  /**
   * Set the basic (mandatory) settings for the requests
   *
   * @param string $spid
   *          The SPID of your account, supported by mPAY24
   * @param string $password
   *          The flexLINK password, supported by mPAY24
   * @param bool $test
   *          TRUE - when you want to use the TEST system
   *          
   *          FALSE - when you want to use the LIVE system
   */
  public function configureFlexLINK($spid, $password, $test) {
    /**
     * An error message, that will be displayed to the user in case you are using the LIVE system
     * @const LIVE_ERROR_MSG
     */
    define('LIVE_ERROR_MSG', "We are sorry, an error occured - please contact the merchant!");
    
    /**
     * The current directory, where the script is runnig from
     * @const __DIR__
     */
    if(! defined('__DIR__'))
      define('__DIR__', dirname(__FILE__));
    
    $this->setSPID($spid);
    $this->setPassword($password);
    $this->setFlexLINKSystem($test);
  }
  
  /**
   * Get the merchant ID, which was set by the function configure($merchantID, $soapPassword, $test, $proxyHost, $proxyPort)
   *
   * @return string
   */
  public function getMerchantID() {
    return substr($this->merchantid, 1);
  }
  
  /**
   * Get the SPID, which was set by the function configureFlexLINK($spid, $password, $test)
   *
   * @return string
   */
  public function getSPID() {
    return $this->spid;
  }
  
  /**
   * Get the system, which should be used for flexLINK (test -> 'test' or live -> 'www')
   *
   * @return string
   */
  public function getFlexLINKSystem() {
    return $this->flexLINKSystem;
  }
  
  /**
   * Get the url, where requests are going to be posted
   *
   * @return string
   */
  public function getEtpURL() {
    return $this->etp_url;
  }
  
  /**
   * Get the request, which was sent to mPAY24 (in XML form)
   *
   * @return string
   */
  public function getRequest() {
    return $this->request;
  }
  
  /**
   * Get the response from mPAY24 (in XML form)
   *
   * @return string
   */
  public function getResponse() {
    return $this->response;
  }
  
  /**
   * Check whether a proxy is used
   *
   * @return bool
   */
  public function proxyUsed() {
    if($this->proxy_host != '' && $this->proxy_port != '')
      return true;
    else
      return false;
  }
  
  /**
   * Set debug modus (FALSE by default)
   *
   * @param bool $debug
   *          TRUE if is turned on, otherwise FALSE
   */
  public function setDebug($debug) {
    $this->debug = $debug;
  }
  
  /**
   * Check whether the debug modus is turned on or off
   *
   * @return bool
   */
  public function getDebug() {
    return $this->debug;
  }
  
  /**
   * In case the test system is used, show die with the real error message, otherwise, show the difined constant error LIVE_ERROR_MSG
   *
   * @param string $msg
   *          The message, which is shown to the user
   */
  public function dieWithMsg($msg) {
    if($this->test)
      die($msg);
    else
      die(LIVE_ERROR_MSG);
  }
  
  /**
   * In case the test system is used, show print the real error message, otherwise, show the difined constant error LIVE_ERROR_MSG
   *
   * @param string $msg
   *          The message, which is shown to the user
   */
  public function printMsg($msg) {
    if($this->test)
      print($msg);
    else
      print(LIVE_ERROR_MSG);
  }
  
  /**
   * Die with an error message, which show the path in case of read/write permission errors
   */
  public function permissionError() {
    $errors = error_get_last();
    $message = $errors['message'];
    $path = substr($message, strpos($message, 'fopen(') + 6, strpos($message, ')') - (strpos($message, 'fopen(') + 6));
    $this->dieWithMsg("Can't open file '$path'! Please set the needed read/write rights!");
  }
  
  /**
   * Get all the payment methods, that are available for the merchant by mPAY24
   *
   * @return ListPaymentMethodsResponse
   */
  public function ListPaymentMethods() {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ListPaymentMethods');
    $operation = $body->appendChild($operation);
    
    $xmlMerchantID = $xml->createElement('merchantID', substr($this->merchantid, 1));
    $xmlMerchantID = $operation->appendChild($xmlMerchantID);
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new ListPaymentMethodsResponse($this->response);
    
    return $result;
  }
  
  /**
   * Start a secure payment through the mPAY24 payment window -
   * the sensible data (credit card numbers, bank account numbers etc)
   * is (will be) not saved in the shop
   *
   * @param ORDER $mdxi
   *          The mdxi xml, which contains the shopping cart
   * @return PaymentResponse
   */
  public function SelectPayment($mdxi) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:SelectPayment');
    $operation = $body->appendChild($operation);
    
    $merchantID = $xml->createElement('merchantID', substr($this->merchantid, 1));
    $merchantID = $operation->appendChild($merchantID);
    
    $xmlMDXI = $xml->createElement('mdxi', htmlspecialchars($mdxi));
    $xmlMDXI = $operation->appendChild($xmlMDXI);
    
    $getDataURL = $xml->createElement('getDataURL', "dummy_getDataURL");
    $getDataURL = $operation->appendChild($getDataURL);
    
    $tid = $xml->createElement('tid', 'tid');
    $tid = $operation->appendChild($tid);
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new PaymentResponse($this->response);
    
    return $result;
  }
  
  /**
   * Start a secure payment using a PROFILE (mPAY24 proSAFE), supported by mPAY24 -
   * a customer profile (you have already created) will be used for the payment.
   * The payment window will not be called, the payment source (for example credit card),
   * which was used from the customer by the last payment will be used for the transaction.
   *
   * @param ORDER $requestString
   *          The order xml, which contains the shopping cart
   * @return PaymentResponse
   */
  public function ProfilePayment($requestString) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:AcceptPayment');
    $operation = $body->appendChild($operation);
    
    $requestXML = new DOMDocument("1.0", "UTF-8");
    $requestXML->formatOutput = true;
    $requestXML->loadXML($requestString);
    
    $requestNode = $requestXML->getElementsByTagName("AcceptPayment")->item(0);
    
    foreach($requestNode->childNodes as $child) {
      $child = $xml->importNode($child, true);
      $operation->appendChild($child);
      
      if($child->nodeName == 'payment')
        $child->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'etp:PaymentPROFILE');
    }
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new PaymentResponse($this->response);
    
    return $result;
  }
  
  /**
   * Start a secure payment using the mPAY24 Tokenizer.
   *
   * @param string $pType
   *          The payment type used for the tokenization (currently supported 'CC')
   * @return PaymentTokenResponse
   */
  public function CreateToken($pType) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:CreatePaymentToken');
    $operation = $body->appendChild($operation);
    
    $merchantID = $xml->createElement('merchantID', substr($this->merchantid, 1));
    $merchantID = $operation->appendChild($merchantID);
    
    $pType = $xml->createElement('pType', $pType);
    $pType = $operation->appendChild($pType);
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new PaymentTokenResponse($this->response);
    
    return $result;
  }
  
  /**
   * Initialize a manual callback to mPAY24 in order to check the information provided by PayPal
   *
   * @param string $tid               The TID used for the transaction
   * @param string $amount            The AMOUNT used for the transaction
   * @param string $currency          The CURRENCY used for the transaction
   * @param string $token             The TOKEN used for the transaction
   * @return PaymentResponse
   */
  public function PayWithToken($tid, $amount, $currency, $token) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
  
    $operation = $xml->createElement('etp:AcceptPayment');
    $operation = $body->appendChild($operation);
    
    $merchantID = $xml->createElement('merchantID', substr($this->merchantid, 1));
    $merchantID = $operation->appendChild($merchantID);

    $xmlTID = $xml->createElement('tid', $tid);
    $xmlTID = $operation->appendChild($xmlTID);
    
    $xmlPType = $xml->createElement('pType', "TOKEN");
    $xmlPType = $operation->appendChild($xmlPType);
    
    $xmlPayment = $xml->createElement('payment');
    $xmlPayment = $operation->appendChild($xmlPayment);
    $xmlPayment->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'etp:PaymentTOKEN');
    
    $xmlAmount = $xml->createElement('amount', $amount);
    $xmlAmount = $xmlPayment->appendChild($xmlAmount);
    
    $xmlCurrency = $xml->createElement('currency', $currency);
    $xmlCurrency = $xmlPayment->appendChild($xmlCurrency);
    
    $xmlToken = $xml->createElement('token', $token);
    $xmlToken = $xmlPayment->appendChild($xmlToken);

    $this->request = $xml->saveXML();
  
    $this->send();
  
    $result = new PaymentResponse($this->response);
  
    return $result;
  }
  
  /**
   * Start an AcceptPayment transaction, supported by mPAY24.
   *
   * @param ORDER $requestString
   *          The order xml, which contains the shopping cart
   * @param string $paymentType
   *          The payment type which will be used for the acceptpayment request (EPS, SOFORT, PAYPAL, MASTERPASS or TOKEN)
   * @return PaymentResponse
   */
  public function AcceptPayment($requestString, $paymentType) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElement('etp:AcceptPayment');
    $operation = $body->appendChild($operation);
    
    $requestXML = new DOMDocument("1.0", "UTF-8");
    $requestXML->formatOutput = true;
    $requestXML->loadXML($requestString);
    
    $requestNode = $requestXML->getElementsByTagName("AcceptPayment")->item(0);
    
    foreach($requestNode->childNodes as $child) {
      $child = $xml->importNode($child, true);
      $operation->appendChild($child);
      
      if($child->nodeName == 'payment')
        $child->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', "etp:Payment$paymentType");
    }
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new PaymentResponse($this->response);
    
    return $result;
  }
  
  /**
   * Initialize a manual callback to mPAY24 in order to check the information provided by PayPal
   *
   * @param string $requestString
   *          The callback request to mPAY24
   * @param string $paymentType
   *          The payment type which will be used for the express checkout (PAYPAL or MASTERPASS)
   * @return PaymentResponse
   */
  public function Callback($requestString, $paymentType) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElement('etp:ManualCallback');
    $operation = $body->appendChild($operation);
    
    $requestXML = new DOMDocument("1.0", "UTF-8");
    $requestXML->formatOutput = true;
    $requestXML->loadXML($requestString);
    
    $requestNode = $requestXML->getElementsByTagName("AcceptPayment")->item(0);
    
    foreach($requestNode->childNodes as $child) {
      $child = $xml->importNode($child, true);
      $operation->appendChild($child);
      
      if($child->nodeName == 'paymentCallback')
        $child->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', "etp:Callback$paymentType");
    }
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new PaymentResponse($this->response);
    
    return $result;
  }
  
  /**
   * Clear a transaction with an amount
   *
   * @param int $mPAYTid
   *          The mPAY24 transaction ID
   * @param int $amount
   *          The amount to be cleared multiplay by 100
   * @param string $currency
   *          3-digit ISO currency code: EUR, USD, etc
   * @return ManagePaymentResponse
   */
  public function ManualClear($mPAYTid, $amount, $currency) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualClear');
    $operation = $body->appendChild($operation);
    
    $merchantID = $xml->createElement('merchantID', substr($this->merchantid, 1));
    $merchantID = $operation->appendChild($merchantID);
    
    $clearingDetails = $xml->createElement('clearingDetails');
    $clearingDetails = $operation->appendChild($clearingDetails);
    
    $xmlMPayTid = $xml->createElement('mpayTID', $mPAYTid);
    $xmlMPayTid = $clearingDetails->appendChild($xmlMPayTid);
    
    $price = $xml->createElement('amount', $amount);
    $price = $clearingDetails->appendChild($price);
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new ManagePaymentResponse($this->response);
    
    return $result;
  }
  
  /**
   * Credit a transaction with an amount
   *
   * @param int $mPAYTid
   *          The mPAY24 transaction ID
   * @param int $amount
   *          The amount to be credited multiplay by 100
   * @param string $currency
   *          3-digit ISO currency code: EUR, USD, etc
   * @param string $customer
   *          The name of the customer, who has paid
   * @return ManagePaymentResponse
   */
  public function ManualCredit($mPAYTid, $amount, $currency, $customer) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualCredit');
    $operation = $body->appendChild($operation);
    
    $merchantID = $xml->createElement('merchantID', substr($this->merchantid, 1));
    $merchantID = $operation->appendChild($merchantID);
    
    $xmlMPayTid = $xml->createElement('mpayTID', $mPAYTid);
    $xmlMPayTid = $operation->appendChild($xmlMPayTid);
    
    $price = $xml->createElement('amount', $amount);
    $price = $operation->appendChild($price);
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new ManagePaymentResponse($this->response);
    
    return $result;
  }
  
  /**
   * Cancel a transaction
   *
   * @param int $mPAYTid
   *          The mPAY24 transaction ID for the transaction you want to cancel
   * @return ManagePaymentResponse
   */
  public function ManualReverse($mPAYTid) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualReverse');
    $operation = $body->appendChild($operation);
    
    $merchantID = $xml->createElement('merchantID', substr($this->merchantid, 1));
    $merchantID = $operation->appendChild($merchantID);
    
    $xmlMPayTid = $xml->createElement('mpayTID', $mPAYTid);
    $xmlMPayTid = $operation->appendChild($xmlMPayTid);
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new ManagePaymentResponse($this->response);
    
    return $result;
  }
  
  /**
   * Get all the information for a transaction, supported by mPAY24
   *
   * @param int $mPAYTid
   *          The mPAY24 transaction ID
   * @param string $tid
   *          The transaction ID from your shop
   * @return TransactionStatusResponse
   */
  public function TransactionStatus($mPAYTid = null, $tid = null) {
    $xml = $this->buildEnvelope();
    $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);
    
    $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:TransactionStatus');
    $operation = $body->appendChild($operation);
    
    $merchantID = $xml->createElement('merchantID', substr($this->merchantid, 1));
    $merchantID = $operation->appendChild($merchantID);
    
    if($mPAYTid) {
      $xmlMPayTid = $xml->createElement('mpayTID', $mPAYTid);
      $xmlMPayTid = $operation->appendChild($xmlMPayTid);
    } else {
      $xmlTid = $xml->createElement('tid', $tid);
      $xmlTid = $operation->appendChild($xmlTid);
    }
    
    $this->request = $xml->saveXML();
    
    $this->send();
    
    $result = new TransactionStatusResponse($this->response);
    
    return $result;
  }
  
  /**
   * Encoded the parameters (AES256-CBC) for the pay link and retunr them
   *
   * @param array $params
   *          The parameters, which are going to be posted to mPAY24
   * @return string
   */
  public function flexLINK($params) {
    $paramsString = "";
    
    foreach($params as $key => $value)
      $paramsString .= "$key=$value&";
    
    $encryptedParams = $this->ssl_encrypt($this->pass, $paramsString);
    
    return $encryptedParams;
  }
  
  /**
   * Set the merchant ID (without 'u')
   *
   * @param string $merchantID
   *          The merchant ID
   */
  private function setMerchantID($merchantID = null) {
    if($merchantID == null)
      $this->merchantid = 'u' . MERCHANT_ID;
    else
      $this->merchantid = 'u' . $merchantID;
  }
  
  /**
   * Set the SPID, in order to make flexLINK transactions
   *
   * @param string $spid
   *          The SPID of your account, supported by mPAY24
   */
  private function setSPID($spid) {
    $this->spid = $spid;
  }
  
  /**
   * Set the Web-Services/SOAP password
   *
   * @param string $pass
   *          The SOAP password, provided by mPAY24
   */
  private function setSoapPassword($pass = null) {
    if(defined("SOAP_PASSWORD"))
      $this->soappass = SOAP_PASSWORD;
    else
      $this->soappass = $pass;
  }
  
  /**
   * Set the flexLINK password
   *
   * @param string $pass
   *          The flexLINK password, provided by mPAY24
   */
  private function setPassword($pass) {
    $this->pass = $pass;
  }
  
  /**
   * Set whether the tets system (true) or the live system (false) will be used for the SOAP requests
   * Set the POST url
   *
   * ("https://test.mpay24.com/app/bin/etpproxy_v14" or
   *
   * "https://www.mpay24.com/app/bin/etpproxy_v14")
   *
   * @param bool $test
   *          TRUE for TEST system and FALSE for LIVE system.
   *          
   */
  private function setSystem($test = null) {
    if($test) {
      $this->test = true;
      $this->etp_url = "https://test.mpay24.com/app/bin/etpproxy_v15";
    } else {
      $this->test = false;
      $this->etp_url = "https://www.mpay24.com/app/bin/etpproxy_v15";
    }
  }
  
  /**
   * Set whether the tets system (true) or the live system (false) will be used for the flexLINK requests
   *
   * @param bool $test
   *          TRUE for TEST system and FALSE for LIVE system.
   */
  private function setFlexLINKSystem($test = null) {
    if($test)
      $this->flexLINKSystem = "test";
    else
      $this->flexLINKSystem = "www";
  }
  
  /**
   * Set the used proxy host and proxy port in case proxy is used
   *
   * @param string $proxy_host
   *          Proxy host
   * @param string $proxy_port
   *          Proxy port
   * @param string $proxy_user
   *          Proxy user
   * @param string $proxy_pass
   *          Proxy pass
   */
  private function setProxySettings($proxy_host = "", $proxy_port = "", $proxy_user = "", $proxy_pass = "") {
    if($proxy_host != "" && $proxy_port != "") {
      $this->proxy_host = $proxy_host;
      $this->proxy_port = $proxy_port;
    }
    
    if($proxy_user != "" && $proxy_pass != "") {
      $this->proxy_user = $proxy_user;
      $this->proxy_pass = $proxy_pass;
    }
  }
  
  /**
   * Set whether to stop cURL from verifying the peer's certificate
   *
   * @param bool $verify_peer
   *          Set as FALSE to stop cURL from verifying the peer's certificate
   */
  private function setVerifyPeer($verify_peer) {
    $this->verify_peer = $verify_peer;
  }
  
  /**
   * Create a DOMDocument and prepare it for SOAP request:
   * set Envelope, NameSpaces, create empty Body
   *
   * @return DOMDocument
   */
  private function buildEnvelope() {
    $this->soap_xml = new DOMDocument("1.0", "UTF-8");
    $this->soap_xml->formatOutput = true;
    
    $envelope = $this->soap_xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Envelope');
    $envelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
    $envelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:etp', 'https://www.mpay24.com/soap/etp/1.5/ETP.wsdl');
    $envelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $envelope = $this->soap_xml->appendChild($envelope);
    
    $body = $this->soap_xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Body');
    $body = $envelope->appendChild($body);
    
    return $this->soap_xml;
  }
  
  /**
   * Create a curl request and send the cretaed SOAP XML
   */
  private function send() {
    $userAgent = 'mPAY24 PHP API $Rev: 6231 $ ($Date:: 2015-03-13 #$)';
    
    if($this->shop != '') {
      $userAgent = $this->shop;
      
      if($this->shopVersion != '')
        $userAgent .= " v. " . $this->shopVersion;
      if($this->shopVersion != '')
        $userAgent .= " - Module v. " . $this->moduleVersion;
    }
    
    $ch = curl_init($this->etp_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_USERPWD, "$this->merchantid:$this->soappass");
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    if($this->debug) {
      $fh = fopen(__DIR__ . "/../logs/curllog.log", 'a+') or $this->permissionError();
      
      curl_setopt($ch, CURLOPT_VERBOSE, 1);
      curl_setopt($ch, CURLOPT_STDERR, $fh);
    }
    
    try {
      curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
      
      if($this->proxy_host !== '' && $this->proxy_port !== '') {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy_host . ':' . $this->proxy_port);
        
        if($this->proxy_user !== '' && $this->proxy_pass !== '')
          curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_user . ':' . $this->proxy_pass);
        
        if($this->verify_peer !== true)
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_peer);
      }
      
      $this->response = curl_exec($ch);
      curl_close($ch);
      
      if($this->debug)
        fclose($fh);
    } catch(Exception $e) {
      if($this->test)
        $dieMSG = "Your request couldn't be sent because of the following error:" . "\n" . curl_error($ch) . "\n" . $e->getMessage() . ' in ' . $e->getFile() . ', line: ' . $e->getLine() . '.';
      else
        $dieMSG = LIVE_ERROR_MSG;
      
      echo $dieMSG;
    }
  }
  
  /**
   * Encode data (AES256-CBC) using a password
   *
   * @param string $pass
   *          The password, used for the encoding
   * @param string $data
   *          The data, that should be encoded
   * @return string
   */
  private function ssl_encrypt($pass, $data) {
    // Set a random salt
    $salt = substr(md5(mt_rand(), true), 8);
    
    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $pad = $block - (strlen($data) % $block);
    
    $data = $data . str_repeat(chr($pad), $pad);
    
    // Setup encryption parameters
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, "", MCRYPT_MODE_CBC, "");
    
    $key_len = mcrypt_enc_get_key_size($td);
    $iv_len = mcrypt_enc_get_iv_size($td);
    
    $total_len = $key_len + $iv_len;
    $salted = '';
    $dx = '';
    
    // Salt the key and iv
    while(strlen($salted) < $total_len) {
      $dx = md5($dx . $pass . $salt, true);
      $salted .= $dx;
    }
    
    $key = substr($salted, 0, $key_len);
    $iv = substr($salted, $key_len, $iv_len);
    
    mcrypt_generic_init($td, $key, $iv);
    $encrypted_data = mcrypt_generic($td, $data);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    
    return chunk_split(array_shift( unpack('H*', 'Salted__' . $salt . $encrypted_data)), 32, "\r\n");
  }
}

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
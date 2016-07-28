<?php
//include_once ("MPay24Api.php");
//include_once ("orderXML.php");

namespace MPayAPI\core;

/**
 * The abstract MPay24Shop class provides abstract functions, which are used from the other functions in order to make a payment or a request to mPAY24
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @version $Id: MPay24Shop.php 6271 2015-04-09 08:38:50Z anna $
 * @filesource MPay24Shop.php
 * @license http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
abstract class MPay24Shop extends Transaction {
  /**
   * The mPAY24API Object, with you are going to work
   *
   * @var $mPay24Api
   */
  var $mPay24Api = null;
  
  /**
   * The constructor, which sets all the initial values, in order to be able making transactions
   *
   * @param int $merchantID
   *          5-digit account number, supported by mPAY24:
   *          TEST accounts - starting with 9
   *          LIVE account - starting with 7
   * @param string $soapPassword
   *          The webservice's password, supported by mPAY24
   * @param bool $test
   *          TRUE - when you want to use the TEST system,
   *          FALSE - when you want to use the LIVE system
   * @param bool $debug
   *          TRUE - when you want to write log files,
   *          FALSE - when you don't want write log files
   * @param string $proxyHost
   *          The host name in case you are behind a proxy server ("" when not)
   * @param int $proxyPort
   *          4-digit port number in case you are behind a proxy server ("" when not)
   *          
   * @param string $proxyUser
   *          The proxy user in case you are behind a proxy server ("" when not)
   * @param string $proxyPass
   *          The proxy password in case you are behind a proxy server ("" when not)
   * @param bool $verfiyPeer
   *          Set as FALSE to stop cURL from verifying the peer's certificate
   */
  function MPay24Shop($merchantID, $soapPassword, $test, $debug = false, $proxyHost = null, $proxyPort = null, $proxyUser = null, $proxyPass = null, $verfiyPeer = true) {
    if(! is_bool($test))
      die("The test parameter '$test' you have given is wrong, it must be boolean value 'true' or 'false'!");
    
    if(! is_bool($debug))
      die("The debug parameter '$debug' you have given is wrong, it must be boolean value 'true' or 'false'!");
    
    if(! is_bool($verfiyPeer))
      die("The verifyPeer parameter '$verfiyPeer' you have given is wrong, it must be boolean value 'true' or 'false'!");
    
    $this->mPay24Api = new MPay24Api();
    
    if($proxyHost == null) {
      $pHost = "";
      $pPort = "";
      $pUser = "";
      $pPass = "";
    } else {
      $pHost = $proxyHost;
      $pPort = $proxyPort;
      
      if($proxyUser == null) {
        $pUser = "";
        $pPass = "";
      } else {
        $pUser = $proxyUser;
        $pPass = $proxyPass;
      }
    }
    
    $this->mPay24Api->configure($merchantID, $soapPassword, $test, $pHost, $pPort, $pUser, $pPass, $verfiyPeer);
    $this->mPay24Api->setDebug($debug);
    
    if(version_compare(phpversion(), '5.0.0', '<') === true || ! in_array('curl', get_loaded_extensions()) || ! in_array('dom', get_loaded_extensions())) {
      $this->mPay24Api->printMsg("ERROR: You don't meet the needed requirements for this example shop.<br>");
      
      if(version_compare(phpversion(), '5.0.0', '<') === true)
        $this->mPay24Api->printMsg("You need PHP version 5.0.0 or newer!<br>");
      if(! in_array('curl', get_loaded_extensions()))
        $this->mPay24Api->printMsg("You need cURL extension!<br>");
      if(! in_array('dom', get_loaded_extensions()))
        $this->mPay24Api->printMsg("You need DOM extension!<br>");
      
      $this->mPay24Api->dieWithMsg("Please load the required extensions!");
    }
    
    if(strlen($merchantID) != 5 || (substr($merchantID, 0, 1) != "7" && substr($merchantID, 0, 1) != "9"))
      $this->mPay24Api->dieWithMsg("The merchant ID '$merchantID' you have given is wrong, it must be 5-digit number and starts with 7 or 9!");
    
    if($proxyPort != null && (! is_numeric($proxyPort) || strlen($proxyPort) != 4))
      $this->mPay24Api->dieWithMsg("The proxy port '$proxyPort' you have given must be numeric!");
    
    if(($proxyHost == null && $proxyHost != $proxyPort) || ($proxyPort == null && $proxyHost != $proxyPort))
      $this->mPay24Api->dieWithMsg("You must setup both variables 'proxyHost' and 'proxyPort'!");
  }
  
  /**
   * Create a transaction and save/return this (in a data base or file system (for example XML))
   *
   * @return Transaction
   */
  abstract function createTransaction();
  
  /**
   * Actualize the transaction, which has a transaction ID = $tid with the values from $args in your shop and return it
   *
   * @param string $tid
   *          The transaction ID you want to update with the confirmation
   * @param array $args
   *          Arrguments with them the transaction is to be updated
   * @param bool $shippingConfirmed
   *          TRUE if the shipping address is confirmed, FALSE - otherwise (in case of PayPal or MasterPass Express Checkout)
   */
  abstract function updateTransaction($tid, $args, $shippingConfirmed);
  
  /**
   * Give the transaction object back, for a transaction which has a transaction ID = $tid
   *
   * @param string $tid
   *          The transaction ID of the transaction you want get
   * @return Transaction
   */
  abstract function getTransaction($tid);
  
  /**
   * Using the ORDER object from order.php, create a MDXI-XML, which is needed for a transaction to be started
   *
   * @param Transaction $transaction
   *          The transaction you want to make a MDXI XML file for
   * @return ORDER
   */
  abstract function createMDXI($transaction);
  
  /**
   * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with profiles to be started
   *
   * @param string $tid
   *          The transaction ID of the transaction you want to make an order transaction XML file for
   * @return XML
   */
  abstract function createProfileOrder($tid);
  
  /**
   * Using the ORDER object from order.php, create a order-xml, which is needed for a backend to backend transaction to be started
   *
   * @param string $tid
   *          The transaction ID of the transaction you want to make an order transaction XML file for
   * @param string $paymentType
   *          The payment type which will be used for the backend to backend payment (EPS, SOFORT, PAYPAL, MASTERPASS or TOKEN)
   * @return XML
   */
  abstract function createBackend2BackendOrder($tid, $paymentType);
  
  /**
   * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with PayPal or MasterPass Express Checkout to be finished
   *
   * @param string $tid
   *          The transaction ID of the transaction you want to make an order transaction XML file for
   * @param string $shippingCosts
   *          The shipping costs amount for the transaction, provided by PayPal or MasterPass, after changing the shipping address
   * @param string $amount
   *          The new amount for the transaction, provided by PayPal or MasterPass, after changing the shipping address
   * @param bool $cancel
   *          TRUE if the a cancelation is wanted after renewing the amounts and FALSE otherwise
   * @return XML
   */
  abstract function createFinishExpressCheckoutOrder($tid, $shippingCosts, $amount, $cancel);
  
  /**
   * Write a log into a file, file system, data base
   *
   * @param string $operation
   *          The operation, which is to log: GetPaymentMethods, Pay, PayWithProfile, Confirmation, UpdateTransactionStatus, ClearAmount, CreditAmount, CancelTransaction, etc.
   * @param string $info_to_log
   *          The information, which is to log: request, response, etc.
   */
  abstract function write_log($operation, $info_to_log);
  
  /**
   * This is an optional function, but it's strongly recomended that you implement it - see details.
   * It should build a hash from the transaction ID of your shop, the amount of the transaction,
   * the currency and the timeStamp of the transaction. The mPAY24 confirmation interface will be called
   * with this hash (parameter name 'token'), so you would be able to check whether the confirmation is
   * really coming from mPAY24 or not. The hash should be then saved in the transaction object, so that
   * every transaction has an unique secret token.
   *
   * @param string $tid
   *          The transaction ID you want to make a secret key for
   * @param string $amount
   *          The amount, reserved for this transaction
   * @param string $currency
   *          The currency (3-digit ISO-Currency-Code) at the moment the transaction is created
   * @param string $timeStamp
   *          The timeStamp at the moment the transaction is created
   * @return string
   */
  abstract function createSecret($tid, $amount, $currency, $timeStamp);
  
  /**
   * Get the secret (hashed) token for a transaction
   *
   * @param string $tid
   *          The transaction ID you want to get the secret key for
   * @return string
   */
  abstract function getSecret($tid);
  
  /**
   * Get a list which includes all the payment methods (activated by mPAY24) for your mechant ID
   *
   * @return ListPaymentMethodsResponse
   */
  function getPaymentMethods() {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    $paymentMethods = $this->mPay24Api->ListPaymentMethods();
    
    if($this->mPay24Api->getDebug()) {
      $this->write_log("GetPaymentMethods", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("GetPaymentMethods", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
    
    return $paymentMethods;
  }
  
  /**
   * Return a redirect URL to start a payment
   *
   * @return PaymentResponse
   */
  function pay() {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    $transaction = $this->createTransaction();
    
    $this->checkTransaction($transaction);
    
    libxml_use_internal_errors(true);
    
    $mdxi = $this->createMDXI($transaction);
    
    if(! $mdxi || ! $mdxi instanceof ORDER)
      $this->mPay24Api->dieWithMsg("To be able to use the MPay24Api you must create an ORDER object (order.php) and fulfill it with a MDXI!");
    
    $mdxiXML = $mdxi->toXML();
    
    if(! $this->mPay24Api->proxyUsed())
      if(! $mdxi->validate()) {
        $errors = "";
        
        foreach(libxml_get_errors() as $error)
          $errors .= trim($error->message) . "<br>";
        
        $this->mPay24Api->dieWithMsg("The schema you have created is not valid!" . "<br><br>" . $errors . "<textarea cols='100' rows='30'>$mdxiXML</textarea>");
      }
    
    $mdxiXML = $mdxi->toXML();
    
    $payResult = $this->mPay24Api->SelectPayment($mdxiXML);
    
    if($this->mPay24Api->getDebug()) {
      $this->write_log("Pay", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("Pay", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
    
    return $payResult;
  }
  
  /**
   * Start a payment with customer profile
   *
   * @return PaymentResponse
   */
  function payWithProfile() {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    $transaction = $this->createTransaction();
    
    $this->checkTransaction($transaction);
    
    $order = $this->createProfileOrder($transaction);
    
    if(! $order || ! $order instanceof ORDER)
      $this->mPay24Api->dieWithMsg("To be able to use the MPay24Api you must create an ORDER object (order.php)!");
    
    $payWithProfileResult = $this->mPay24Api->ProfilePayment($order->toXML());
    
    if($this->mPay24Api->getDebug()) {
      $this->write_log("PayWithProfile", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("PayWithProfile", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
    
    return $payWithProfileResult;
  }
  
  /**
   * Start a backend to backend payment
   *
   * @param string $paymentType
   *          The payment type which will be used for the payment (EPS, SOFORT, PAYPAL, MASTERPASS or TOKEN)
   * @return PaymentResponse
   */
  function payBackend2Backend($paymentType) {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    if($paymentType !== 'PAYPAL' && $paymentType !== 'MASTERPASS' && $paymentType !== 'EPS'  && $paymentType !== 'SOFORT' && $paymentType !== 'TOKEN')
      die("The payment type '$paymentType' is not allowed! Allowed are: 'EPS', 'SOFORT', 'PAYPAL', 'MASTERPASS', 'TOKEN'!");
    
    $transaction = $this->createTransaction();
    
    $this->checkTransaction($transaction);
    
    $order = $this->createBackend2BackendOrder($transaction, $paymentType);
    
    if(! $order || ! $order instanceof ORDER)
      $this->mPay24Api->dieWithMsg("To be able to use the MPay24Api you must create an ORDER object (order.php)!");
    
    $payBackend2BackendResult = $this->mPay24Api->AcceptPayment($order->toXML(), $paymentType);
    
    if($this->mPay24Api->getDebug()) {
      $this->write_log("PayBackend2Backend", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("PayBackend2Backend", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
    
    return $payBackend2BackendResult;
  }
  
  /**
   * Finish the payment, started with PayPal Express Checkout - reserve, bill or cancel it: Whether are you going to reserve or bill a payment is setted at the beginning of the payment.
   * With the 'cancel' parameter you are able also to cancel the transaction
   *
   * @param string $tid
   *          The transaction ID in the shop
   * @param int $shippingCosts
   *          The shippingcosts for the transaction multiply by 100
   * @param int $amount
   *          The amount you want to reserve/bill multiply by 100
   * @param string $cancel
   *          ALLOWED: "true" or "false" - in case of 'true' the transaction will be canceled, otherwise reserved/billed
   * @param string $paymentType
   *          The payment type which will be used for the express checkout (PAYPAL or MASTERPASS)
   * @return PaymentResponse
   */
  function finishExpressCheckoutPayment($tid, $shippingCosts, $amount, $cancel, $paymentType) {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    if($cancel !== "true" && $cancel !== "false")
      $this->mPay24Api->dieWithMsg("The allowed values for the parameter 'cancel' by finishing a PayPal (Express Checkout) payment are 'true' or 'false'!");
    
    if($paymentType !== 'PAYPAL' && $paymentType !== 'MASTERPASS')
      die("The payment type '$paymentType' is not allowed! Allowed are: 'PAYPAL' and 'MASTERPASS'");
    
    $transaction = $this->getTransaction($tid);
    
    $this->checkTransaction($transaction);
    
    $mPAYTid = $transaction->MPAYTID;
    
    if(! $mPAYTid)
      $this->mPay24Api->dieWithMsg("The transaction '$tid' you want to finish with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!");
    
    if(! $amount || ! is_numeric($amount))
      $this->mPay24Api->dieWithMsg("The amount '$amount' you are trying to pay by '$paymentType' is not valid!");
    
    if(! $shippingCosts || ! is_numeric($shippingCosts))
      $this->mPay24Api->dieWithMsg("The shipping costs '$shippingCosts' you are trying to set are not valid!");
    
    $order = $this->createFinishExpressCheckoutOrder($transaction, $shippingCosts, $amount, $cancel);
    
    if(! $order || ! $order instanceof ORDER)
      $this->mPay24Api->dieWithMsg("To be able to use the MPay24Api you must create an ORDER object (order.php)!");
    
    $finishExpressCheckoutResult = $this->mPay24Api->Callback($order->toXML(), $paymentType);
    
    if($this->mPay24Api->getDebug()) {
      $this->write_log("FinishExpressCheckoutResult", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("FinishExpressCheckoutResult", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
    
    return $finishExpressCheckoutResult;
  }
  
  /**
   * Return a redirect URL to include in your web page
   *
   * @param string $paymentType
   *          The payment type which will be used for the express checkout (CC)
   *
   * @return PaymentTokenResponse
   */
  function payWithToken($paymentType) {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
  
    if($paymentType !== 'CC')
      die("The payment type '$paymentType' is not allowed! Currently allowed is only: 'CC'");
    
    $tokenResult = $this->mPay24Api->CreateToken($paymentType);
  
    if($this->mPay24Api->getDebug()) {
      $this->write_log("CreateToken", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("CreateToken", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
  
    return $tokenResult;
  }
  
  /**
   * Finish the payment, started with PayPal Express Checkout - reserve, bill or cancel it: Whether are you going to reserve or bill a payment is setted at the beginning of the payment.
   * With the 'cancel' parameter you are able also to cancel the transaction
   *
   * @param string $tid
   *          The transaction ID in the shop
   * @param int $amount
   *          The amount you want to reserve/bill multiply by 100
   * @param int $currency
   *          The currency you want to use for the transaction in ISO (EUR, USD, etc)
   * @param string $token
   *          The token which was returned by the payWithToken function
   * @return PaymentResponse
   */
  function finishTokenPayment($tid, $amount, $currency, $token) {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
  
    $transaction = $this->getTransaction($tid);
  
    $this->checkTransaction($transaction);
    
    if(! $amount || ! is_numeric($amount))
      $this->mPay24Api->dieWithMsg("The amount '$amount' you are trying to pay is not valid!");
  
    $finishTokenPaymentResult = $this->mPay24Api->PayWithToken($tid, $amount, $currency, $token);
  
    if($this->mPay24Api->getDebug()) {
      $this->write_log("FinishTokenPaymentResult", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("FinishTokenPaymentResult", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
  
    return $finishTokenPaymentResult;
  }
  
  /**
   * Proceed the confirmation call
   *
   * @param string $tid
   *          The transaction ID in the shop
   * @param array $args
   *          A list with the arguments, provided with the confirmation
   */
  function confirm($tid, $args) {
    $to_log = '';
    $shippingConfirmed = "";
    
    foreach($args as $name => $value)
      $to_log .= $name . " = " . $value . "\n";
    
    if($this->mPay24Api->getDebug())
      $this->write_log("Confirmation for transaction '" . $tid . "'\n", utf8_encode($to_log) . "\n");
    
    $transactionStatus = $this->updateTransactionStatus($tid);
    
    $newArgs = $transactionStatus->getParams();
    
    foreach($newArgs as $name => $value)
      $to_log .= $name . " = " . $value . "\n";
    
    if($this->mPay24Api->getDebug())
      $this->write_log("Status for transaction " . $tid . ":", utf8_encode($to_log) . "\n");
    
    if($transactionStatus->getParam("SHIPPING_ADDR")) {
      $order = new DOMDocument();
      $order->loadXML($transactionStatus->getParam("SHIPPING_ADDR"));
    }
    
    if(isset($order)) {
      $shipping = $order->getElementsByTagName("Shipping")->item(0);
      $shippingConfirmed = $shipping->getAttribute("confirmed");
    }
    
    if($this->getSecret($tid) == $args['token']) {
      if($shippingConfirmed == "false") {
        $newArgs["SHIPP_NAME"] = $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Name")->item(0)->nodeValue;
        $newArgs["SHIPP_STREET"] = $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Street")->item(0)->nodeValue;
        
        if($order->getElementsByTagName("Shipping")->item(0)->hasAttribute("Street2"))
          $newArgs["SHIPP_STREET2"] = $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Street2")->item(0)->nodeValue;
        
        $newArgs["SHIPP_ZIP"] = $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Zip")->item(0)->nodeValue;
        $newArgs["SHIPP_CITY"] = $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("City")->item(0)->nodeValue;
        $newArgs["SHIPP_COUNTRY"] = $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Country")->item(0)->getAttribute("code");
        return $this->updateTransaction($tid, $newArgs, false);
      } else
        return $this->updateTransaction($tid, $newArgs, true);
    }
  }
  
  /**
   * Get the transaction's current information - see details
   *
   * An array with all the data (by mPAY24) for this transaction (STATUS, CURRENCY, PRICE, APPR_CODE, etc) will be returned.
   * Possible values for the STATUS attribute:
   *
   * * RESERVED - in case the authorization was successful but not cleared yet
   * * BILLED - in case the authorization was successful and amount was cleared
   * * CREDITED - in case amount was credited
   * * REVERSED - in case the transaction was canceled
   * * SUSPENDED - in case the transaction is not fully compleated yet
   * * NOT FOUND - in case there is not such a transaction in the mPAY24 database
   * * ERROR - in case the transaction was not successful
   *
   * @param string $tid
   *          The transaction ID (in your shop), for the transaction you are asking for
   * @return array
   */
  function updateTransactionStatus($tid) {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    $transaction = $this->getTransaction($tid);
    
    $this->checkTransaction($transaction);
    
    if(! $transaction->MPAYTID || ! is_numeric($transaction->MPAYTID)) {
      $tidTransactionStatusResult = $this->mPay24Api->TransactionStatus(null, $tid);
      
      if($this->mPay24Api->getDebug()) {
        $this->write_log("TidTransactionStatus", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
        $this->write_log("TidTransactionStatus", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
      }
      
      if($tidTransactionStatusResult->getParam("SHIPPING_ADDR")) {
        $order = new DOMDocument();
        $order->loadXML($tidTransactionStatusResult->getParam("SHIPPING_ADDR"));
      }
      
      if(isset($order)) {
        $shipping = $order->getElementsByTagName("Shipping")->item(0);
        $shippingConfirmed = $shipping->getAttribute("confirmed");
        
        if($shippingConfirmed == "false") {
          $tidTransactionStatusResult->setParam("shippingConfirmed", false);
          $tidTransactionStatusResult->setParam("SHIPP_NAME", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Name")->item(0)->nodeValue);
          $tidTransactionStatusResult->setParam("SHIPP_STREET", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Street")->item(0)->nodeValue);
          if($tidTransactionStatusResult->getParam("SHIPP_STREET2"))
            $tidTransactionStatusResult->setParam("SHIPP_STREET2", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Street2")->item(0)->nodeValue);
          $tidTransactionStatusResult->setParam("SHIPP_ZIP", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Zip")->item(0)->nodeValue);
          $tidTransactionStatusResult->setParam("SHIPP_CITY", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("City")->item(0)->nodeValue);
          $tidTransactionStatusResult->setParam("SHIPP_COUNTRY", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Country")->item(0)->getAttribute("code"));
        } else
          $tidTransactionStatusResult->setParam("shippingConfirmed", true);
      } else
        $tidTransactionStatusResult->setParam("shippingConfirmed", true);
      
      return $tidTransactionStatusResult;
    } else {
      $mPAYTidTransactionStatusResult = $this->mPay24Api->TransactionStatus($transaction->MPAYTID, null);
      
      if($this->mPay24Api->getDebug()) {
        $this->write_log("mPAYTidTransactionStatus", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
        $this->write_log("mPAYTidTransactionStatus", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
      }
      
      if($mPAYTidTransactionStatusResult->getParam("SHIPPING_ADDR")) {
        $order = new DOMDocument();
        $order->loadXML($mPAYTidTransactionStatusResult->getParam("SHIPPING_ADDR"));
      }
      
      if(isset($order)) {
        $shipping = $order->getElementsByTagName("Shipping")->item(0);
        $shippingConfirmed = $shipping->getAttribute("confirmed");
        
        if($shippingConfirmed == "false") {
          $mPAYTidTransactionStatusResult->setParam("shippingConfirmed", false);
          $mPAYTidTransactionStatusResult->setParam("SHIPP_NAME", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Name")->item(0)->nodeValue);
          $mPAYTidTransactionStatusResult->setParam("SHIPP_STREET", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Street")->item(0)->nodeValue);
          $mPAYTidTransactionStatusResult->setParam("SHIPP_STREET2", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Street2")->item(0)->nodeValue);
          $mPAYTidTransactionStatusResult->setParam("SHIPP_ZIP", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Zip")->item(0)->nodeValue);
          $mPAYTidTransactionStatusResult->setParam("SHIPP_CITY", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("City")->item(0)->nodeValue);
          $mPAYTidTransactionStatusResult->setParam("SHIPP_COUNTRY", $order->getElementsByTagName("Shipping")->item(0)->getElementsByTagName("Country")->item(0)->getAttribute("code"));
        } else
          $mPAYTidTransactionStatusResult->setParam("shippingConfirmed", true);
      } else
        $mPAYTidTransactionStatusResult->setParam("shippingConfirmed", true);
      
      return $mPAYTidTransactionStatusResult;
    }
  }
  
  /**
   * Clear an amount of an authorized transaction
   *
   * @param string $tid
   *          The transaction ID, for the transaction you want to clear
   * @param int $amount
   *          The amount you want to clear multiply by 100
   */
  function clearAmount($tid, $amount) {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    $transaction = $this->getTransaction($tid);
    
    $this->checkTransaction($transaction);
    
    $mPAYTid = $transaction->MPAYTID;
    $currency = $transaction->CURRENCY;
    
    if(! $mPAYTid)
      $this->mPay24Api->dieWithMsg("The transaction '$tid' you want to clear with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!");
    
    if(! $amount || ! is_numeric($amount))
      $this->mPay24Api->dieWithMsg("The amount '$amount' you are trying to clear is not valid!");
    
    if(! $currency || strlen($currency) != 3)
      $this->mPay24Api->dieWithMsg("The currency code '$currency' for the amount you are trying to clear is not valid (3-digit ISO-Currency-Code)!");
    
    $clearAmountResult = $this->mPay24Api->ManualClear($mPAYTid, $amount, $currency);
    
    if($this->mPay24Api->getDebug()) {
      $this->write_log("ClearAmount", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("ClearAmount", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
    
    return $clearAmountResult;
  }
  
  /**
   * Credit an amount of a billed transaction
   *
   * @param string $tid
   *          The transaction ID, for the transaction you want to credit
   * @param int $amount
   *          The amount you want to credit multiply by 100
   */
  function creditAmount($tid, $amount) {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    $transaction = $this->getTransaction($tid);
    
    $this->checkTransaction($transaction);
    
    $mPAYTid = $transaction->MPAYTID;
    $currency = $transaction->CURRENCY;
    $customer = $transaction->CUSTOMER;
    
    if(! $mPAYTid)
      $this->mPay24Api->dieWithMsg("The transaction '$tid' you want to credit with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!");
    
    if(! $amount || ! is_numeric($amount))
      $this->mPay24Api->dieWithMsg("The amount '$amount' you are trying to credit is not valid!");
    
    if(! $currency || strlen($currency) != 3)
      $this->mPay24Api->dieWithMsg("The currency code '$currency' for the amount you are trying to credit is not valid (3-digit ISO-Currency-Code)!");
    
    $creditAmountResult = $this->mPay24Api->ManualCredit($mPAYTid, $amount, $currency, $customer);
    
    if($this->mPay24Api->getDebug()) {
      $this->write_log("CreditAmount", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("CreditAmount", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
    
    return $creditAmountResult;
  }
  
  /**
   * Cancel a authorized transaction
   *
   * @param string $tid
   *          The transaction ID, for the transaction you want to cancel
   */
  function cancelTransaction($tid) {
    if(! $this->mPay24Api)
      die("You are not allowed to define a constructor in the child class of MPay24Shop!");
    
    $transaction = $this->getTransaction($tid);
    
    $this->checkTransaction($transaction);
    
    $mPAYTid = $transaction->MPAYTID;
    
    if(! $mPAYTid)
      $this->mPay24Api->dieWithMsgie("The transaction '$tid' you want to cancel with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!");
    
    $cancelTransactionResult = $this->mPay24Api->ManualReverse($mPAYTid);
    
    if($this->mPay24Api->getDebug()) {
      $this->write_log("CancelTransaction", "REQUEST to " . $this->mPay24Api->getEtpURL() . " - " . str_replace("><", ">\n<", $this->mPay24Api->getRequest()) . "\n");
      $this->write_log("CancelTransaction", "RESPONSE - " . str_replace("><", ">\n<", $this->mPay24Api->getResponse()) . "\n");
    }
    
    return $cancelTransactionResult;
  }
  
  /**
   * Check if the a transaction is created, whether the object is from type Transaction and whether the mandatory settings (TID and PRICE) of a transaction are setted
   *
   * @param Transaction $tid
   *          The transaction, which should be checked
   */
  private function checkTransaction($transaction) {
    if(! $transaction || ! $transaction instanceof Transaction)
      $this->mPay24Api->dieWithMsg("To be able to use the MPay24Api you must create a Transaction object, which contains at least TID and PRICE!");
    else if(! $transaction->TID)
      $this->mPay24Api->dieWithMsg("The Transaction must contain TID!");
    else if(! $transaction->PRICE)
      $this->mPay24Api->dieWithMsg("The Transaction must contain PRICE!");
  }
}
?>

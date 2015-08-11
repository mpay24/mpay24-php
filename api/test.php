<?php
include_once("MPay24Shop.php");

/**
 * The class MyFlexLINK extends the abstract class MPay24flexLINK and implements the log-fuction for this class
 *
 * @author              mPAY24 GmbH <support@mpay24.com>
 * @version             $Id: test.php 6271 2015-04-09 08:38:50Z anna $
 * @filesource          test.php
 * @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class MyFlexLINK extends MPay24flexLINK {
  /**
   * Write a flexLINK log into flexLINK.log
   * @param             string              $info_to_log                  The information, which is to log: request, response, etc.
   */
  function write_flexLINK_log($info_to_log) {
//     This function should be only implemented in case the flexLINK functionality was implmented and will be used
//     $fh = fopen("flexLINK.log", 'a+') or die("can't open file");
//     $MessageDate = date("Y-m-d H:i:s");
//     $Message= $MessageDate." ".$_SERVER['SERVER_NAME']." mPAY24 : ";
//     $result = $Message."$info_to_log\n";
//     fwrite($fh, $result);
//     fclose($fh);
  }
}

/**
 * The class MyShop extends the abstract class MPay24Shop and implements some of the basic functions in order to be able to make a payment
 *
 * @author              mPAY24 GmbH <support@mpay24.com>
 * @version             $Id: test.php 6271 2015-04-09 08:38:50Z anna $
 * @filesource          test.php
 * @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
class MyShop extends MPay24Shop {
  /**
   * The transaction ID
   * @var               $tid
   */
  var $tid                        = 'My Order';
  /**
   * The amount for the transaction
   * @var               $price
   */
  var $price                      = 10.00;
  
  /**
   * The currency for the transaction
   * @var               $currency
   */
  var $currency                   = "GBP";
  
  /**
   * The language for the transaction
   * @var               $currency
   */
  var $language                   = "EN";
  
  /**
   * The customer (name) for the transaction
   * @var               $customer
   */
  var $customer                   = "John & Joan Doe";
  /**
   * The customer e-mail for the transaction 
   * @var               $customer_email
   */
  var $customer_email             = "test@example.com";
  /**
   * The customer ID for the transaction 
   * @var               $customer_id
   */
  var $customer_id                = "customer_12345";
  /**
   * The customer street for the transaction
   * @var               $customer_street
   */
  var $customer_street            = "Mainstreet 123";
  /**
   * The customer street for the transaction
   * @var               $customer_street2
   */
  var $customer_street2           = "Flat Nr 5";
  /**
   * The customer ZIP code for the transaction
   * @var               $customer_zip
   */
  var $customer_zip               = "12345";
  /**
   * The customer city for the transaction
   * @var               $customer_city
   */
  var $customer_city              = "London";
  /**
   * The customer country ISO code for the transaction
   * @var               $customer_country
   */
  var $customer_country           = "GB";
  
  /**
   * The products for the transaction
   * @var               $products
   */
  var $products                   = array(
    1 => array("productNr"=>"abcdef123", "description"=>"Erstes Produkt", "package"=>"Enthält 2 Einheiten", "quantity"=>1, "itemPrice"=>"1.00", "tax" => "0.20"),
    2 => array("productNr"=>"xyz456", "description"=>"Zweites Produkt", "package"=>"Enthält 1 Einheit", "quantity"=>2, "itemPrice"=>"2.00", "tax" => "0.40"),
    3 => array("productNr"=>"sdlfkji", "description"=>"Drittes Produkt", "package"=>"", "quantity"=>1, "itemPrice"=>"5.00", "tax" => "1.00")
  );

  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderStyle
   */
  var $mPAY24OrderStyle                       = "margin-left: auto; margin-right: auto;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderLogoStyle
   */
  var $mPAY24OrderLogoStyle                   = "";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderPageHeaderStyle
   */
  var $mPAY24OrderPageHeaderStyle             = "background-color: #FFF;margin-bottom:14px;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderPageCaptionStyle
   */
  var $mPAY24OrderPageCaptionStyle            = "background-color:#FFF;background:transparent;color:#647378;padding-left:0px;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderPageStyle
   */
  var $mPAY24OrderPageStyle                   = "border:1px solid #838F93;background-color:#FFF;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderInputFieldsStyle
   */
  var $mPAY24OrderInputFieldsStyle            = "background-color:#ffffff;border:1px solid #DDE1E7;padding:2px 0px;margin-bottom:5px;width:100%;max-width:200px;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderDropDownListsStyle
   */
  var $mPAY24OrderDropDownListsStyle          = "padding:2px 0px;margin-bottom:5px;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderButtonsStyle
   */
  var $mPAY24OrderButtonsStyle                = "background-color: #005AC1;border: none;color: #FFFFFF;cursor: pointer;font-size:10px;font-weight:bold;padding:5px 10px;text-transform:uppercase;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderErrorsStyle
   */
  var $mPAY24OrderErrorsStyle                 = "background-color: #FFF;padding: 10px 0px;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderSuccessTitleStyle
   */
  var $mPAY24OrderSuccessTitleStyle           = "background-color: #FFF;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderErrorTitleStyle
   */
  var $mPAY24OrderErrorTitleStyle             = "background-color: #FFF;";
  /**
   * The order styles for the transaction
   * @var               $mPAY24OrderFooterStyle
   */
  var $mPAY24OrderFooterStyle                 = "";
  
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartStyle
   */
  var $mPAY24ShoppingCartStyle                = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartHeader
   */
  var $mPAY24ShoppingCartHeader               = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartHeaderStyle
   */
  var $mPAY24ShoppingCartHeaderStyle          = "background-color:#FFF;margin-bottom:14px;color:#647378";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartCaptionStyle
   */
  var $mPAY24ShoppingCartCaptionStyle         = "background-color:#FFF;background:transparent;color:#647378;padding-left:0px;font-size:14px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartNumberHeader
   */
  var $mPAY24ShoppingCartNumberHeader         = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartNumberStyle
   */
  var $mPAY24ShoppingCartNumberStyle          = "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartProductNumberHeader
   */
  var $mPAY24ShoppingCartProductNumberHeader  = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartProductNumberStyle
   */
  var $mPAY24ShoppingCartProductNumberStyle   = "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartDescriptionHeader
   */
  var $mPAY24ShoppingCartDescriptionHeader    = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartDescriptionStyle
   */
  var $mPAY24ShoppingCartDescriptionStyle     = "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartPackageHeader
   */
  var $mPAY24ShoppingCartPackageHeader        = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartPackageStyle
   */
  var $mPAY24ShoppingCartPackageStyle         = "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartQuantityHeader
   */
  var $mPAY24ShoppingCartQuantityHeader       = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartQuantityStyle
   */
  var $mPAY24ShoppingCartQuantityStyle        = "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemPriceHeader
   */
  var $mPAY24ShoppingCartItemPriceHeader      = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemPriceStyle
   */
  var $mPAY24ShoppingCartItemPriceStyle       = "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartPriceHeader
   */
  var $mPAY24ShoppingCartPriceHeader          = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemPriceStyle
   */
  var $mPAY24ShoppingCartPriceStyle           = "width:80px;background-color:#FFF;color:#647378;border: 1px solid #838F93;text-transform:uppercase;padding:5px;text-align:center;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemNumberStyleOdd
   */
  var $mPAY24ShoppingCartItemNumberStyleOdd           = "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemNumberStyleEven
   */
  var $mPAY24ShoppingCartItemNumberStyleEven          = "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemProductNumberStyleOdd
   */
  var $mPAY24ShoppingCartItemProductNumberStyleOdd    = "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemProductNumberStyleEven
   */
  var $mPAY24ShoppingCartItemProductNumberStyleEven   = "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemDescriptionStyleOdd
   */
  var $mPAY24ShoppingCartItemDescriptionStyleOdd      = "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemDescriptionStyleEven
   */
  var $mPAY24ShoppingCartItemDescriptionStyleEven     = "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemPackageStyleOdd
   */
  var $mPAY24ShoppingCartItemPackageStyleOdd          = "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemPackageStyleEven
   */
  var $mPAY24ShoppingCartItemPackageStyleEven         = "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemQuantityStyleOdd
   */
  var $mPAY24ShoppingCartItemQuantityStyleOdd         = "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemQuantityStyleEven
   */
  var $mPAY24ShoppingCartItemQuantityStyleEven        = "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemItemPriceStyleOdd
   */
  var $mPAY24ShoppingCartItemItemPriceStyleOdd        = "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemItemPriceStyleEven
   */
  var $mPAY24ShoppingCartItemItemPriceStyleEven       = "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemPriceStyleOdd
   */
  var $mPAY24ShoppingCartItemPriceStyleOdd            = "background-color: #FFF;color: #647378; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartItemPriceStyleEven
   */
  var $mPAY24ShoppingCartItemPriceStyleEven           = "background-color: #FFF;color: #327F98; border: 1px solid #838F93;text-align:center;padding:5px 0px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartSubTotalHeader
   */
  var $mPAY24ShoppingCartSubTotalHeader               = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartSubTotalHeaderStyle
   */
  var $mPAY24ShoppingCartSubTotalHeaderStyle          = "background-color:#FFF;color: #647378;padding:3px;font-weight:normal;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartSubTotalStyle
   */
  var $mPAY24ShoppingCartSubTotalStyle                = "background-color:#FFF;color:#647378;border:none;font-weight:normal;padding:3px 20px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartDiscountHeader
   */
  var $mPAY24ShoppingCartDiscountHeader               = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartDiscountHeaderStyle
   */
  var $mPAY24ShoppingCartDiscountHeaderStyle          = "background-color: #FFF; color: #647378;font-weight:normal;padding:3px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartDiscountStyle
   */
  var $mPAY24ShoppingCartDiscountStyle                = "background-color:#FFF;color:#647378;border:none;padding:3px 20px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartShippingCostsHeader
   */
  var $mPAY24ShoppingCartShippingCostsHeader          = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartShippingCostsHeaderStyle
   */
  var $mPAY24ShoppingCartShippingCostsHeaderStyle     = "background-color: #FFF; color: #647378;font-weight:normal;padding:3px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartShippingCostsStyle
   */
  var $mPAY24ShoppingCartShippingCostsStyle           = "background-color:#FFF;color:#647378;border:none;padding:3px 20px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartTaxHeader
   */
  var $mPAY24ShoppingCartTaxHeader                    = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartTaxHeaderStyle
   */
  var $mPAY24ShoppingCartTaxHeaderStyle               = "background-color:#FFF;color: #647378;padding:3px;font-weight:normal;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartTaxStyle
   */
  var $mPAY24ShoppingCartTaxStyle                     = "background-color:#FFF;color:#647378;border:none;font-weight:normal;padding:3px 20px;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24PriceHeader
   */
  var $mPAY24PriceHeader                              = "";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24PriceHeaderStyle
   */
  var $mPAY24PriceHeaderStyle                         = "background-color:#FFF;color: #647378;padding:3px;font-weight:normal;border-top: 1px solid #838F93;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24PriceStyle
   */
  var $mPAY24PriceStyle                               = "background-color:#FFF;color:#005AC1;border:none;padding:4px;font-weight:bold;padding:3px 20px;font-size:14px;border-top: 1px solid #838F93;";
  /**
   * The shopping cart styles for the transaction
   * @var               $mPAY24ShoppingCartDescription
   */
  var $mPAY24ShoppingCartDescription                  = "";
  
  /**
   * Actualize the transaction, writing all the transaction's parameters into result.txt
   * @param             string              $tid                          The transaction ID you want to update with the confirmation
   * @param             array               $args                         Arrguments with them the transaction is to be updated
   * @param             bool                $shippingConfirmed            TRUE if the shipping address is confirmed, FALSE - otherwise (in case of PayPal Express Checkout)
   */
  function updateTransaction($tid, $args, $shippingConfirmed) {
    try {
      $fh = fopen("result.txt", 'w') or die("can't open file");

      $result = "TID : " . $tid . "\n\n" . sizeof($args) . " transaction arguments:\n\n";

      foreach($args as $key => $value)
        $result.= $key . " = " . $value . "\n";

      fwrite($fh, $result);
      fclose($fh);
      echo "OK:\n the confirmation was successfully recieved";
    } catch (Exception $e) {
      echo "ERROR:\n" . $e->getMessage() . "\n" . $e->getTrace();
    }
  }

  /**
   * Give the transaction object back, after the required parameters (TID and PRICE) was set
   * @param             string              $tid                          The transaction ID of the transaction you want get
   * @return            Transaction
   */
  function getTransaction($tid) {
    $transaction = new Transaction($this->tid);
    $transaction->PRICE = $this->price;
    $transaction->CURRENCY = $this->currency;
    return $transaction;
  }

  /**
   * NOT IMPLEMENTED
   *
   * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with profiles to be started
   * @param             string              $tid                          The transaction ID of the transaction you want to make an order transaction XML file for
   * @return            XML
   */
  function createProfileOrder($tid) {}

  /**
   * NOT IMPLEMENTED
   *
   * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with PayPal Express Checkout to be started
   * @param             string              $tid                          The transaction ID of the transaction you want to make an order transaction XML file for
   * @return            XML
   */
  function createExpressCheckoutOrder($tid) {}

  /**
   * NOT IMPLEMENTED
   *
   * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with PayPal Express Checkout to be finished
   * @param             string              $tid                          The transaction ID of the transaction you want to make an order transaction XML file for
   * @param             string              $shippingCosts                The shipping costs amount for the transaction, provided by PayPal, after changing the shipping address
   * @param             string              $amount                       The new amount for the transaction, provided by PayPal, after changing the shipping address
   * @param             bool                $cancel                       TRUE if the a cancelation is wanted after renewing the amounts and FALSE otherwise
   * @return            XML
   */
  function createFinishExpressCheckoutOrder($tid, $s, $a, $c) {}

  /**
   * Write a mpay24 log into log.log
   * @param             string              $operation                    The operation, which is to log: GetPaymentMethods, Pay, PayWithProfile, Confirmation, UpdateTransactionStatus, ClearAmount, CreditAmount, CancelTransaction, etc.
   * @param             string              $info_to_log                  The information, which is to log: request, response, etc.
   */
  function write_log($operation, $info_to_log) {
    $fh = fopen("log.log", 'a+') or die("can't open file");
    $MessageDate = date("Y-m-d H:i:s");
    $Message= $MessageDate." ".$_SERVER['SERVER_NAME']." mPAY24 : ";
    $result = $Message."$operation : $info_to_log\n";
    fwrite($fh, $result);
    fclose($fh);
  }

  /**
   * NOT IMPLEMENTED
   *
   * This is an optional function, but it's strongly recomended that you implement it - see details.
   * It should build a hash from the transaction ID of your shop, the amount of the transaction,
   * the currency and the timeStamp of the transaction. The mPAY24 confirmation interface will be called
   * with this hash (parameter name 'token'), so you would be able to check whether the confirmation is
   * really coming from mPAY24 or not. The hash should be then saved in the transaction object, so that
   * every transaction has an unique secret token.
   * @param             string              $tid                          The transaction ID you want to make a secret key for
   * @param             string              $amount                       The amount, reserved for this transaction
   * @param             string              $currency                     The timeStamp at the moment the transaction is created
   * @param             string              $timeStamp                    The timeStamp at the moment the transaction is created
   * @return            string
   */
  function createSecret($tid, $amount, $currency, $timeStamp) {}

  /**
   * NOT IMPLEMENTED
   *
   * Get the secret (hashed) token for a transaction
   * @param             string              $tid                          The transaction ID you want to get the secret key for
   * @return            string
   */
  function getSecret($tid) {}

  /**
   * Create a transaction with the reuqired transaction's parameters - TID and PRICE
   * @return            Transaction
   */
  function createTransaction() {
    $transaction = new Transaction($this->tid);
    $transaction->PRICE = $this->price;
    $transaction->CURRENCY = $this->currency;
    $transaction->LANGUAGE = $this->language;
    $transaction->CUSTOMER = $this->customer;
    $transaction->CUSTOMER_EMAIL = $this->customer_email;
    $transaction->CUSTOMER_ID = $this->customer_id;
    
    return $transaction;
  }

  /**
   * Using the ORDER object, create a exmaple MDXI-XML
   * @param             Transaction         $transaction                  The transaction you want to make a MDXI XML file for
   * @return            ORDER
   */
  function createMDXI($transaction) {
    $mdxi = new ORDER();

    //Order design settings for the mPAY24 pay page
    $mdxi->Order->setStyle($this->mPAY24OrderStyle);
    $mdxi->Order->setLogoStyle($this->mPAY24OrderLogoStyle);
    $mdxi->Order->setPageHeaderStyle($this->mPAY24OrderPageHeaderStyle);
    $mdxi->Order->setPageCaptionStyle($this->mPAY24OrderPageCaptionStyle);
    $mdxi->Order->setPageStyle($this->mPAY24OrderPageStyle);
    $mdxi->Order->setInputFieldsStyle($this->mPAY24OrderInputFieldsStyle);
    $mdxi->Order->setDropDownListsStyle($this->mPAY24OrderDropDownListsStyle);
    $mdxi->Order->setButtonsStyle($this->mPAY24OrderButtonsStyle);
    $mdxi->Order->setErrorsStyle($this->mPAY24OrderErrorsStyle);
    $mdxi->Order->setSuccessTitleStyle($this->mPAY24OrderSuccessTitleStyle);
    $mdxi->Order->setErrorTitleStyle($this->mPAY24OrderErrorTitleStyle);
    $mdxi->Order->setFooterStyle($this->mPAY24OrderFooterStyle);

    $mdxi->Order->ClientIP = $_SERVER ['REMOTE_ADDR'];
    
    $mdxi->Order->Tid = $transaction->TID;

    $mdxi->Order->TemplateSet = "WEB";
    $mdxi->Order->TemplateSet->setLanguage ( $transaction->LANGUAGE );
    $mdxi->Order->TemplateSet->setCSSName ( "MOBILE" );
    
//     Enable only specific payments
//     $mdxi->Order->PaymentTypes->setEnable (true);
//     $mdxi->Order->PaymentTypes->Payment(1)->setType("CC");
//     $mdxi->Order->PaymentTypes->Payment(1)->setBrand("VISA");
//     $mdxi->Order->PaymentTypes->Payment(2)->setType("PAYPAL");
    
    //Shopping cart design settings for the mPAY24 pay page
    $mdxi->Order->ShoppingCart->setStyle ($this->mPAY24ShoppingCartStyle);
    $mdxi->Order->ShoppingCart->setHeader ($this->mPAY24ShoppingCartHeader);
    $mdxi->Order->ShoppingCart->setHeaderStyle ($this->mPAY24ShoppingCartHeaderStyle);
    $mdxi->Order->ShoppingCart->setCaptionStyle ($this->mPAY24ShoppingCartCaptionStyle);
    $mdxi->Order->ShoppingCart->setNumberHeader ($this->mPAY24ShoppingCartNumberHeader);
    $mdxi->Order->ShoppingCart->setNumberStyle ($this->mPAY24ShoppingCartNumberStyle);
    $mdxi->Order->ShoppingCart->setProductNrHeader ($this->mPAY24ShoppingCartProductNumberHeader);
    $mdxi->Order->ShoppingCart->setProductNrStyle ($this->mPAY24ShoppingCartProductNumberStyle);
    $mdxi->Order->ShoppingCart->setDescriptionHeader ($this->mPAY24ShoppingCartDescriptionHeader);
    $mdxi->Order->ShoppingCart->setDescriptionStyle ($this->mPAY24ShoppingCartDescriptionStyle);
    $mdxi->Order->ShoppingCart->setPackageHeader ($this->mPAY24ShoppingCartPackageHeader);
    $mdxi->Order->ShoppingCart->setPackageStyle ($this->mPAY24ShoppingCartPackageStyle);
    $mdxi->Order->ShoppingCart->setQuantityHeader ($this->mPAY24ShoppingCartQuantityHeader);
    $mdxi->Order->ShoppingCart->setQuantityStyle ($this->mPAY24ShoppingCartQuantityStyle);
    $mdxi->Order->ShoppingCart->setItemPriceHeader ($this->mPAY24ShoppingCartItemPriceHeader);
    $mdxi->Order->ShoppingCart->setItemPriceStyle ($this->mPAY24ShoppingCartItemPriceStyle);
    $mdxi->Order->ShoppingCart->setPriceHeader ($this->mPAY24ShoppingCartPriceHeader);
    $mdxi->Order->ShoppingCart->setPriceStyle ($this->mPAY24ShoppingCartPriceStyle);
    
    $mdxi->Order->ShoppingCart->Description =  ($this->mPAY24ShoppingCartDescription);
    
    for($i = 1; $i <= sizeof ( $this->products ); $i ++) {
      $mdxi->Order->ShoppingCart->Item ( ($i) )->Number = $i;
//       $mdxi->Order->ShoppingCart->Item ( ($i) )->ProductNr =  ( $this->products [$i] ['productNr'] );
      $mdxi->Order->ShoppingCart->Item ( ($i) )->Description = $this->products [$i] ["description"];
//       $mdxi->Order->ShoppingCart->Item ( ($i) )->Package = ($this->products [$i] ["package"]);
      $mdxi->Order->ShoppingCart->Item ( ($i) )->Quantity = $this->products [$i] ["quantity"];
      $mdxi->Order->ShoppingCart->Item ( ($i) )->ItemPrice = number_format ( $this->products [$i] ["itemPrice"], 2, '.', '' );
      $mdxi->Order->ShoppingCart->Item ( ($i) )->ItemPrice->setTax ( number_format ( $this->products [$i] ["tax"], 2, '.', '' ) );

      $mdxi->Order->ShoppingCart->Item ( ($i) )->Price = number_format ( $this->products [$i] ["quantity"] * $this->products [$i] ["tax"], 2, '.', '' );
    
      if (($i % 2)) {
        $mdxi->Order->ShoppingCart->Item ( ($i) )->Number->setStyle ($this->mPAY24ShoppingCartItemNumberStyleEven);
//         $mdxi->Order->ShoppingCart->Item ( ($i) )->ProductNr->setStyle ($this->mPAY24ShoppingCartItemProductNumberStyleEven);
        $mdxi->Order->ShoppingCart->Item ( ($i) )->Description->setStyle ($this->mPAY24ShoppingCartItemDescriptionStyleEven);
//         $mdxi->Order->ShoppingCart->Item ( ($i) )->Package->setStyle ($this->mPAY24ShoppingCartItemPackageStyleEven);
        $mdxi->Order->ShoppingCart->Item ( ($i) )->Quantity->setStyle ($this->mPAY24ShoppingCartItemQuantityStyleEven);
        $mdxi->Order->ShoppingCart->Item ( ($i) )->ItemPrice->setStyle ($this->mPAY24ShoppingCartItemItemPriceStyleEven);
        $mdxi->Order->ShoppingCart->Item ( ($i) )->Price->setStyle ($this->mPAY24ShoppingCartItemPriceStyleEven);
      } elseif (! ($i % 2)) {
        $mdxi->Order->ShoppingCart->Item ( ($i) )->Number->setStyle ($this->mPAY24ShoppingCartItemNumberStyleOdd);
//         $mdxi->Order->ShoppingCart->Item ( ($i) )->ProductNr->setStyle ($this->mPAY24ShoppingCartItemProductNumberStyleOdd);
        $mdxi->Order->ShoppingCart->Item ( ($i) )->Description->setStyle ($this->mPAY24ShoppingCartItemDescriptionStyleOdd);
//         $mdxi->Order->ShoppingCart->Item ( ($i) )->Package->setStyle ($this->mPAY24ShoppingCartItemPackageStyleOdd);
        $mdxi->Order->ShoppingCart->Item ( ($i) )->Quantity->setStyle ($this->mPAY24ShoppingCartItemQuantityStyleOdd);
        $mdxi->Order->ShoppingCart->Item ( ($i) )->ItemPrice->setStyle ($this->mPAY24ShoppingCartItemItemPriceStyleOdd);
        $mdxi->Order->ShoppingCart->Item ( ($i) )->Price->setStyle ($this->mPAY24ShoppingCartItemPriceStyleOdd);
      }
    }
    
    $mdxi->Order->ShoppingCart->SubTotal ( 1, number_format ( "10.00", 2, '.', '' ) );
    $mdxi->Order->ShoppingCart->SubTotal ( 1 )->setHeader ($this->mPAY24ShoppingCartSubTotalHeader);
    $mdxi->Order->ShoppingCart->SubTotal ( 1 )->setHeaderStyle ($this->mPAY24ShoppingCartSubTotalHeaderStyle);
    $mdxi->Order->ShoppingCart->SubTotal ( 1 )->setStyle ($this->mPAY24ShoppingCartSubTotalStyle);
    
    $mdxi->Order->ShoppingCart->ShippingCosts ( 1, number_format ( "5.00", 2, '.', '' ) );
    $mdxi->Order->ShoppingCart->ShippingCosts ( 1 )->setHeader ($this->mPAY24ShoppingCartShippingCostsHeader);
    $mdxi->Order->ShoppingCart->ShippingCosts ( 1 )->setHeaderStyle ($this->mPAY24ShoppingCartShippingCostsHeaderStyle);
    $mdxi->Order->ShoppingCart->ShippingCosts ( 1 )->setStyle ($this->mPAY24ShoppingCartShippingCostsStyle);

    $mdxi->Order->ShoppingCart->ShippingCosts ( 1 )->setTax ( number_format ( "1.00", 2, '.', '' ) );
    
    $mdxi->Order->ShoppingCart->Tax ( 1, number_format ( "2.00", 2, '.', '' ) );
    $mdxi->Order->ShoppingCart->Tax ( 1 )->setHeader ($this->mPAY24ShoppingCartTaxHeader);
    $mdxi->Order->ShoppingCart->Tax ( 1 )->setHeaderStyle ($this->mPAY24ShoppingCartTaxHeaderStyle);
    $mdxi->Order->ShoppingCart->Tax ( 1 )->setStyle ($this->mPAY24ShoppingCartTaxStyle);
    
    $mdxi->Order->ShoppingCart->Discount ( 1, '-' . number_format ( "5.00", 2, '.', '' ) );
    $mdxi->Order->ShoppingCart->Discount ( 1 )->setHeader ($this->mPAY24ShoppingCartDiscountHeader);
    $mdxi->Order->ShoppingCart->Discount ( 1 )->setHeaderStyle ($this->mPAY24ShoppingCartDiscountHeaderStyle);
    $mdxi->Order->ShoppingCart->Discount ( 1 )->setStyle ($this->mPAY24ShoppingCartDiscountStyle);
    
    $mdxi->Order->Price = $transaction->PRICE;
    $mdxi->Order->Price->setHeader ($this->mPAY24PriceHeader);
    $mdxi->Order->Price->setHeaderStyle ($this->mPAY24PriceHeaderStyle);
    $mdxi->Order->Price->setStyle ($this->mPAY24PriceStyle);
    
    $mdxi->Order->Currency = $transaction->CURRENCY;

    $mdxi->Order->Customer = $transaction->CUSTOMER;
    $mdxi->Order->Customer->setId($transaction->CUSTOMER_ID);
    $mdxi->Order->Customer->setUseProfile("true");
    
    $mdxi->Order->BillingAddr->setMode ("ReadOnly");
    $mdxi->Order->BillingAddr->Name = $transaction->CUSTOMER;
    $mdxi->Order->BillingAddr->Street = $this->customer_street;
    $mdxi->Order->BillingAddr->Street2 = $this->customer_street2;
    $mdxi->Order->BillingAddr->Zip = $this->customer_zip;
    $mdxi->Order->BillingAddr->City = $this->customer_city;
    $mdxi->Order->BillingAddr->Country->setCode ($this->customer_country);
    $mdxi->Order->BillingAddr->Email = $transaction->CUSTOMER_EMAIL;
    
    $mdxi->Order->ShippingAddr->setMode ( "ReadOnly" );
    $mdxi->Order->ShippingAddr->Name = $transaction->CUSTOMER;
    $mdxi->Order->ShippingAddr->Street = $this->customer_street;
    $mdxi->Order->ShippingAddr->Street2 = $this->customer_street2;
    $mdxi->Order->ShippingAddr->Zip = $this->customer_zip;
    $mdxi->Order->ShippingAddr->City = $this->customer_city;
    $mdxi->Order->ShippingAddr->Country->setCode ($this->customer_country);
    $mdxi->Order->ShippingAddr->Email = $transaction->CUSTOMER_EMAIL;
    
    $mdxi->Order->URL->Success = substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], '/')) . "/success.php";
    $mdxi->Order->URL->Error = substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], '/')) . "/error.php";
    $mdxi->Order->URL->Confirmation = substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], '/')) . "/confirm.php?token=";
    $mdxi->Order->URL->Cancel = substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], '/')) . "/index.html";

    $myFile = "MDXI.xml";
    $fh = fopen($myFile, 'w') or die("can't open file");
    fwrite($fh, $mdxi->toXML());
    fclose($fh);

    return $mdxi;
  }
}

if(isset($_POST["submitPay"])) {
  $myShop = new MyShop('MerchantID', 'SOAPPassword', TRUE, TRUE);
  $result = $myShop->pay();

  if($result->getGeneralResponse()->getStatus() == "OK")
    header('Location: ' . $result->getLocation());
  else
    echo "Return Code: " . $result->getGeneralResponse()->getReturnCode();
} 
// elseif(isset($_POST["submitFlexLINK"])){
//   $myLink = new MyFlexLINK('MerchantID', 'SOAPPassword', TRUE, TRUE);
//   $encryptedParams = $myLink->getEncryptedParams(
//                                                   "my invoice number",
//                                                   "1.00",
//                                                   "GBP",
//                                                   "EN",
//                                                   "MY USER FIELD",
//                                                   "ReadOnly",
//                                                   "F",
//                                                   "Jon & Joan Doe",
//                                                   "Mainstreet 123",
//                                                   "Flat Nr 5",
//                                                   "12345",
//                                                   "London",
//                                                   "GB",
//                                                   "test@example.com",
//                                                   "0044123465789",
//                                                   substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], '/')) . "/success.php",
//                                                   substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], '/')) . "/error.php",
//                                                   substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], '/')) . "/confirm.php?token="
//                                                 );
//   $link = $myLink->getPayLink($encryptedParams);

//   echo "<a href='$link'>$link</a>";
//   echo "<br/><img src='https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=".urlencode($link)."' title='flexLINK' />";
// }
?>

<?php
namespace mPay24;

use mPay24\Responses\PaymentResponse;
use mPay24\Responses\PaymentTokenResponse;
use mPay24\Responses\ListPaymentMethodsResponse;

/**
 * The abstract MPAY24 class provides abstract functions, which are used from the other functions in order to make a payment or a request to mPAY24
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @version $Id: MPAY24.php 6271 2015-04-09 08:38:50Z anna $
 * @filesource MPAY24.php
 * @license MIT
 */
class MPAY24 extends Transaction
{
    const CURL_LOG = './logs/curllog.log';
    const MPAY24_LOG = './logs/mpay24.log';

    /**
     * @var MPAY24SDK|null
     */
    var $mPAY24SDK = null;

    /**
     * MPAY24 constructor.
     * @param string $merchantID
     * @param string $soapPassword
     * @param bool $test
     * @param bool $debug
     * @param null $proxyHost
     * @param null $proxyPort
     * @param null $proxyUser
     * @param null $proxyPass
     * @param bool $verfiyPeer
     * @param bool $enableCurlLog
     */
    function __construct( $merchantID = '9****', $soapPassword = '**********', $test = true, $debug = true, $proxyHost = null, $proxyPort = null, $proxyUser = null, $proxyPass = null, $verfiyPeer = true, $enableCurlLog = false )
    {
        if ( !is_bool($test) ) {
            die("The test parameter '$test' you have given is wrong, it must be boolean value 'true' or 'false'!");
        }

        if ( !is_bool($debug) ) {
            die("The debug parameter '$debug' you have given is wrong, it must be boolean value 'true' or 'false'!");
        }

        if ( !is_bool($verfiyPeer) ) {
            die("The verifyPeer parameter '$verfiyPeer' you have given is wrong, it must be boolean value 'true' or 'false'!");
        }

        $this->mPAY24SDK = new MPAY24SDK();

        if ( $proxyHost == null ) {
            $pHost = "";
            $pPort = "";
            $pUser = "";
            $pPass = "";
        } else {
            $pHost = $proxyHost;
            $pPort = $proxyPort;

            if ( $proxyUser == null ) {
                $pUser = "";
                $pPass = "";
            } else {
                $pUser = $proxyUser;
                $pPass = $proxyPass;
            }
        }

        $this->mPAY24SDK->configure($merchantID, $soapPassword, $test, $pHost, $pPort, $pUser, $pPass, $verfiyPeer);
        $this->mPAY24SDK->setDebug($debug);
        $this->mPAY24SDK->enableCurlLog = $enableCurlLog;

        if ( version_compare(phpversion(), '5.0.0', '<') === true || !in_array('curl', get_loaded_extensions()) || !in_array('dom', get_loaded_extensions()) ) {
            $this->mPAY24SDK->printMsg("ERROR: You don't meet the needed requirements for this example shop.<br>");

            if ( version_compare(phpversion(), '5.0.0', '<') === true ) {
                $this->mPAY24SDK->printMsg("You need PHP version 5.0.0 or newer!<br>");
            }

            if ( !in_array('curl', get_loaded_extensions()) ) {
                $this->mPAY24SDK->printMsg("You need cURL extension!<br>");
            }

            if ( !in_array('dom', get_loaded_extensions()) ) {
                $this->mPAY24SDK->printMsg("You need DOM extension!<br>");
            }

            $this->mPAY24SDK->dieWithMsg("Please load the required extensions!");
        }

        if ( strlen($merchantID) != 5 || (substr($merchantID, 0, 1) != "7" && substr($merchantID, 0, 1) != "9") ) {
            $this->mPAY24SDK->dieWithMsg("The merchant ID '$merchantID' you have given is wrong, it must be 5-digit number and starts with 7 or 9!");
        }

        if ( $proxyPort != null && (!is_numeric($proxyPort) || strlen($proxyPort) != 4) ) {
            $this->mPAY24SDK->dieWithMsg("The proxy port '$proxyPort' you have given must be numeric!");
        }

        if ( ($proxyHost == null && $proxyHost != $proxyPort) || ($proxyPort == null && $proxyHost != $proxyPort) ) {
            $this->mPAY24SDK->dieWithMsg("You must setup both variables 'proxyHost' and 'proxyPort'!");
        }
    }

    /**
     * Create a transaction and save/return this (in a data base or file system (for example XML))
     */
    function createTransaction() { }

    /**
     * Actualize the transaction, which has a transaction ID = $tid with the values from $args in your shop and return it
     *
     * @param string $tid The transaction ID you want to update with the confirmation
     * @param array $args Arguments with them the transaction is to be updated
     * @param bool $shippingConfirmed TRUE if the shipping address is confirmed, FALSE - otherwise (in case of PayPal or MasterPass Express Checkout)
     */
    function updateTransaction( $tid, $args, $shippingConfirmed ) { }

    /**
     * Give the transaction object back, for a transaction which has a transaction ID = $tid
     *
     * @param string $tid The transaction ID of the transaction you want get
     */
    function getTransaction( $tid ) { }

    /**
     * Using the ORDER object from order.php, create a MDXI-XML, which is needed for a transaction to be started
     *
     * @param Transaction $transaction The transaction you want to make a MDXI XML file for
     */
    function createMDXI( $transaction ) { }

    /**
     * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with profiles to be started
     *
     * @param string $tid The transaction ID of the transaction you want to make an order transaction XML file for
     */
    function createProfileOrder( $tid ) { }

    /**
     * Using the ORDER object from order.php, create a order-xml, which is needed for a backend to backend transaction to be started
     *
     * @param string $tid The transaction ID of the transaction you want to make an order transaction XML file for
     * @param string $paymentType The payment type which will be used for the backend to backend payment (EPS, SOFORT, PAYPAL, MASTERPASS or TOKEN)
     */
    function createBackend2BackendOrder( $tid, $paymentType ) { }

    /**
     * Using the ORDER object from order.php, create a order-xml, which is needed for a transaction with PayPal or MasterPass Express Checkout to be finished
     *
     * @param string $tid The transaction ID of the transaction you want to make an order transaction XML file for
     * @param string $shippingCosts The shipping costs amount for the transaction, provided by PayPal or MasterPass, after changing the shipping address
     * @param string $amount The new amount for the transaction, provided by PayPal or MasterPass, after changing the shipping address
     * @param bool $cancel TRUE if the a cancellation is wanted after renewing the amounts and FALSE otherwise
     */
    function createFinishExpressCheckoutOrder( $tid, $shippingCosts, $amount, $cancel ) { }

    /**
     * Write a log into a file, file system, data base
     *
     * @param string $operation The operation, which is to log: GetPaymentMethods, Pay, PayWithProfile, Confirmation, UpdateTransactionStatus, ClearAmount, CreditAmount, CancelTransaction, etc.
     * @param string $info_to_log The information, which is to log: request, response, etc.
     */
    function write_log( $operation, $info_to_log )
    {
        $fh = fopen(__DIR__ . '/' . self::MPAY24_LOG, 'a+') or die("can't open file");
        $MessageDate = date("Y-m-d H:i:s");
        $Message = $MessageDate." ".$_SERVER['SERVER_NAME']." mPAY24 : ";
        $result = $Message."$operation : $info_to_log\n";
        fwrite($fh, $result);
        fclose($fh);
    }

    /**
     * This is an optional function, but it's strongly recomended that you implement it - see details.
     * It should build a hash from the transaction ID of your shop, the amount of the transaction,
     * the currency and the timeStamp of the transaction. The mPAY24 confirmation interface will be called
     * with this hash (parameter name 'token'), so you would be able to check whether the confirmation is
     * really coming from mPAY24 or not. The hash should be then saved in the transaction object, so that
     * every transaction has an unique secret token.
     *
     * @param string $tid The transaction ID you want to make a secret key for
     * @param string $amount The amount, reserved for this transaction
     * @param string $currency The currency (3-digit ISO-Currency-Code) at the moment the transaction is created
     * @param string $timeStamp The timeStamp at the moment the transaction is created
     */
    function createSecret( $tid, $amount, $currency, $timeStamp ) { }

    /**
     * Get the secret (hashed) token for a transaction
     *
     * @param string $tid The transaction ID you want to get the secret key for
     */
    function getSecret($tid) { }

    /**
     * Get a list which includes all the payment methods (activated by mPAY24) for your mechant ID
     *
     * @return ListPaymentMethodsResponse
     */
    function listPaymentMethods()
    {
        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        $paymentMethods = $this->mPAY24SDK->ListPaymentMethods();

        if ( $this->mPAY24SDK->getDebug() ) {
            $this->write_log( "GetPaymentMethods", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "GetPaymentMethods", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $paymentMethods;
    }

    /**
     * Return a redirect URL to start a payment
     *
     * @param $mdxi
     * @return PaymentResponse
     */
    function selectPayment( $mdxi )
    {
        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        libxml_use_internal_errors(true);

        if ( !$mdxi || !$mdxi instanceof ORDER ) {
            $this->mPAY24SDK->dieWithMsg("To be able to use the MPay24Api you must create an ORDER object (order.php) and fulfill it with a MDXI!");
        }

        $mdxiXML = $mdxi->toXML();

        if ( !$this->mPAY24SDK->proxyUsed() ) {
            if ( !$mdxi->validate() ) {
                $errors = "";

                foreach ( libxml_get_errors() as $error ) {
                    $errors .= trim($error->message)."<br>";
                }

                $this->mPAY24SDK->dieWithMsg("The schema you have created is not valid!"."<br><br>".$errors."<textarea cols='100' rows='30'>$mdxiXML</textarea>");
            }
        }

        $mdxiXML = $mdxi->toXML();

        $payResult = $this->mPAY24SDK->SelectPayment($mdxiXML);

        if ($this->mPAY24SDK->getDebug()) {
            $this->write_log( "SelectPayment", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "SelectPayment", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $payResult;
    }

    /**
     *
     * Start a backend to backend payment
     *
     * @param string $paymentType The payment type which will be used for the payment (EPS, SOFORT, PAYPAL, MASTERPASS or TOKEN)
     * @param $tid
     * @param $payment
     * @param $additional
     * @return PaymentResponse
     */
    function acceptPayment( $paymentType, $tid, $payment, $additional )
    {
        if (!$this->mPAY24SDK) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        $payBackend2BackendResult = $this->mPAY24SDK->AcceptPayment($paymentType, $tid, $payment, $additional);

        if ($this->mPAY24SDK->getDebug()) {
            $this->write_log( "AcceptPayment", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "AcceptPayment", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $payBackend2BackendResult;
    }

    /**
     * @param null $mpaytid
     * @param null $tid
     * @return Responses\TransactionStatusResponse
     */
    function transactionStatus( $mpaytid = null, $tid = null )
    {
        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        $result = $this->mPAY24SDK->TransactionStatus($mpaytid, $tid);

        if ( $this->mPAY24SDK->getDebug() ) {
            $this->write_log( "AcceptPayment", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "AcceptPayment", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $result;
    }

    /**
     * Finish the payment, started with PayPal Express Checkout - reserve, bill or cancel it: Whether are you going to reserve or bill a payment is setted at the beginning of the payment.
     * With the 'cancel' parameter you are able also to cancel the transaction
     *
     * @param string $tid The transaction ID in the shop
     * @param int $shippingCosts The shippingcosts for the transaction multiply by 100
     * @param int $amount The amount you want to reserve/bill multiply by 100
     * @param string $cancel ALLOWED: "true" or "false" - in case of 'true' the transaction will be canceled, otherwise reserved/billed
     * @param string $paymentType The payment type which will be used for the express checkout (PAYPAL or MASTERPASS)
     * @return PaymentResponse
     */
    function manualCallback( $tid, $shippingCosts, $amount, $cancel, $paymentType )
    {
        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        if ( $cancel !== "true" && $cancel !== "false" ) {
            $this->mPAY24SDK->dieWithMsg("The allowed values for the parameter 'cancel' by finishing a PayPal (Express Checkout) payment are 'true' or 'false'!");
        }

        if ( $paymentType !== 'PAYPAL' && $paymentType !== 'MASTERPASS' ) {
            die("The payment type '$paymentType' is not allowed! Allowed are: 'PAYPAL' and 'MASTERPASS'");
        }

        $mPAYTid = $transaction->MPAYTID; // ToDo: find from where this came from..

        if ( !$mPAYTid ) {
            $this->mPAY24SDK->dieWithMsg("The transaction '$tid' you want to finish with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!");
        }

        if ( !$amount || !is_numeric($amount) ) {
            $this->mPAY24SDK->dieWithMsg("The amount '$amount' you are trying to pay by '$paymentType' is not valid!");
        }

        if ( !$shippingCosts || !is_numeric($shippingCosts) ) {
            $this->mPAY24SDK->dieWithMsg("The shipping costs '$shippingCosts' you are trying to set are not valid!");
        }

        $order = $this->createFinishExpressCheckoutOrder($transaction, $shippingCosts, $amount, $cancel);

        if ( !$order || !$order instanceof ORDER ) {
            $this->mPAY24SDK->dieWithMsg("To be able to use the MPay24Api you must create an ORDER object (order.php)!");
        }

        $finishExpressCheckoutResult = $this->mPAY24SDK->ManualCallback($order->toXML(), $paymentType);

        if ( $this->mPAY24SDK->getDebug() ) {
            $this->write_log( "FinishExpressCheckoutResult", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "FinishExpressCheckoutResult", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $finishExpressCheckoutResult;
    }

    /**
     * Return a redirect URL to include in your web page
     *
     * @param string $paymentType The payment type which will be used for the express checkout (CC)
     * @param array|null Additional parameters
     * @return PaymentTokenResponse
     */
    function createPaymentToken($paymentType, $additional = null)
    {
        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        if ( $paymentType !== 'CC' ) {
            die("The payment type '$paymentType' is not allowed! Currently allowed is only: 'CC'");
        }

        $tokenResult = $this->mPAY24SDK->CreateTokenPayment($paymentType, $additional);

        if ( $this->mPAY24SDK->getDebug() ) {
            $this->write_log( "CreatePaymentToken", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "CreatePaymentToken", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $tokenResult;
    }

    /**
     * Clear an amount of an authorized transaction
     *
     * @param string $tid The transaction ID, for the transaction you want to clear
     * @param int $amount The amount you want to clear multiply by 100
     * @return Responses\ManagePaymentResponse
     */
    function manualClear( $tid, $amount )
    {
        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        $mPAYTid = $transaction->MPAYTID; // ToDo: again...find from where this came from..
        $currency = $transaction->CURRENCY;

        if ( !$mPAYTid ) {
            $this->mPAY24SDK->dieWithMsg(
                "The transaction '$tid' you want to clear with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!"
            );
        }

        if ( !$amount || !is_numeric($amount) ) {
            $this->mPAY24SDK->dieWithMsg("The amount '$amount' you are trying to clear is not valid!");
        }

        if ( !$currency || strlen($currency) != 3 ) {
            $this->mPAY24SDK->dieWithMsg("The currency code '$currency' for the amount you are trying to clear is not valid (3-digit ISO-Currency-Code)!");
        }

        $clearAmountResult = $this->mPAY24SDK->ManualClear($mPAYTid, $amount, $currency);

        if ( $this->mPAY24SDK->getDebug() ) {
            $this->write_log( "ClearAmount", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "ClearAmount", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $clearAmountResult;
    }

    /**
     * Credit an amount of a billed transaction
     *
     * @param string $tid The transaction ID, for the transaction you want to credit
     * @param int $amount The amount you want to credit multiply by 100
     * @return Responses\ManagePaymentResponse
     */
    function manualCredit( $tid, $amount )
    {
        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        $mPAYTid = $transaction->MPAYTID; // ToDo: again...find from where this came from..
        $currency = $transaction->CURRENCY;
        $customer = $transaction->CUSTOMER;

        if (!$mPAYTid) {
            $this->mPAY24SDK->dieWithMsg("The transaction '$tid' you want to credit with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!");
        }

        if ( !$amount || !is_numeric($amount) ) {
            $this->mPAY24SDK->dieWithMsg("The amount '$amount' you are trying to credit is not valid!");
        }

        if ( !$currency || strlen($currency) != 3 ) {
            $this->mPAY24SDK->dieWithMsg(
                "The currency code '$currency' for the amount you are trying to credit is not valid (3-digit ISO-Currency-Code)!"
            );
        }

        $creditAmountResult = $this->mPAY24SDK->ManualCredit($mPAYTid, $amount, $currency, $customer);

        if ( $this->mPAY24SDK->getDebug() ) {
            $this->write_log( "CreditAmount", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "CreditAmount", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $creditAmountResult;
    }

    /**
     * Cancel a authorized transaction
     *
     * @param string $tid The transaction ID, for the transaction you want to cancel
     * @return Responses\ManagePaymentResponse
     */
    function cancelTransaction( $tid )
    {
        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPAY24!");
        }

        $mPAYTid = $transaction->MPAYTID; // ToDo: again...find from where this came from..

        if (!$mPAYTid) {
            $this->mPAY24SDK->dieWithMsgie("The transaction '$tid' you want to cancel with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!");
        }

        $cancelTransactionResult = $this->mPAY24SDK->ManualReverse($mPAYTid);

        if ( $this->mPAY24SDK->getDebug() ) {
            $this->write_log( "CancelTransaction", sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())) );
            $this->write_log( "CancelTransaction", sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())) );
        }

        return $cancelTransactionResult;
    }

    /**
     * Check if the a transaction is created, whether the object is from type Transaction and whether the mandatory settings (TID and PRICE) of a transaction are setted
     *
     * @param Transaction $transaction The transaction, which should be checked
     */
    private function checkTransaction( $transaction )
    {
        if ( !$transaction || !$transaction instanceof Transaction ) {
            $this->mPAY24SDK->dieWithMsg("To be able to use the MPay24Api you must create a Transaction object, which contains at least TID and PRICE!");
        } else {
            if ( !$transaction->TID ) {
                $this->mPAY24SDK->dieWithMsg("The Transaction must contain TID!");
            } else {
                if ( !$transaction->PRICE ) {
                    $this->mPAY24SDK->dieWithMsg("The Transaction must contain PRICE!");
                }
            }
        }
    }
}
<?php

namespace Mpay24;

use Mpay24\Responses\ListPaymentMethodsResponse;
use Mpay24\Responses\PaymentResponse;
use Mpay24\Responses\PaymentTokenResponse;

/**
 * The Mpay24Soap class provides functions, which are used to make a payment or a request to mPAY24
 *
 * Class Mpay24Soap
 * @package    Mpay24
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource Mpay24Soap.php
 * @license    MIT
 */
class Mpay24Soap
{
    /**
     * @var Mpay24Sdk|null
     */
    var $mpay24Sdk = null;

    /**
     * Mpay24Soap constructor.
     */
    public function __construct()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_a($args[0], Mpay24Config::class)) {
            $config = $args[0];
        } else {
            $config = new Mpay24Config($args);
        }

        $this->mpay24Sdk = new Mpay24Sdk($config);

        $this->mpay24Sdk->checkRequirements(true, true, false);
    }

    /**
     * Get a list which includes all the payment methods (activated by mPAY24) for your mechant ID
     *
     * @return ListPaymentMethodsResponse
     */
    public function listPaymentMethods()
    {
        $this->integrityCheck();

        $paymentMethods = $this->mpay24Sdk->listPaymentMethods();

        $this->recordedLastMessageExchange("GetPaymentMethods");

        return $paymentMethods;
    }

    /**
     * Return a redirect URL to start a payment
     *
     * @param $mdxi
     *
     * @return PaymentResponse
     */
    public function selectPayment($mdxi)
    {
        $this->integrityCheck();

        libxml_use_internal_errors(true);

        if (!$mdxi || !$mdxi instanceof Mpay24Order) {
            $this->mpay24Sdk->dieWithMsg("To be able to use the Mpay24Api you must create an Mpay24Order object (Mpay24Order.php) and fulfill it with a MDXI!");
        }

        $mdxiXML = $mdxi->toXML();

        if (!$this->mpay24Sdk->proxyUsed()) {
            if (!$mdxi->validate()) {
                $errors = "";

                foreach (libxml_get_errors() as $error) {
                    $errors .= trim($error->message) . "<br>";
                }

                $this->mpay24Sdk->dieWithMsg("The schema you have created is not valid!" . "<br><br>" . $errors . "<textarea cols='100' rows='30'>$mdxiXML</textarea>");
            }
        }

        $mdxiXML = $mdxi->toXML();

        $payResult = $this->mpay24Sdk->selectPayment($mdxiXML);

        $this->recordedLastMessageExchange('SelectPayment');

        return $payResult;
    }

    /**
     * Start a backend to backend payment
     *
     * @param string $paymentType The payment type which will be used for the payment (EPS, SOFORT, PAYPAL, MASTERPASS or TOKEN)
     * @param        $tid
     * @param        $payment
     * @param        $additional
     *
     * @return PaymentResponse
     */
    public function acceptPayment($paymentType, $tid, $payment, $additional)
    {
        $this->integrityCheck();
        $payBackend2BackendResult = $this->mpay24Sdk->acceptPayment($paymentType, $tid, $payment, $additional);
        $this->recordedLastMessageExchange('AcceptPayment');

        return $payBackend2BackendResult;
    }

    /**
     * Get the status for a transaction by the unique mPAYTID number
     *
     * @param string $mpaytid
     *
     * @return Responses\TransactionStatusResponse
     */
    public function transactionStatusByMpay24Tid($mpaytid)
    {
        return $this->transactionStatus($mpaytid);
    }

    /**
     * Get the status for the last transaction with the given merchant TID number
     *
     * @param string $tid
     *
     * @return Responses\TransactionStatusResponse
     */
    public function transactionStatusByTid($tid)
    {
        return $this->transactionStatus(null, $tid);
    }

    /**
     * Finish the payment, started with PayPal Express Checkout - reserve, bill or cancel it: Whether are you going to reserve or bill a payment is setted at the beginning of the payment.
     * With the 'cancel' parameter you are able also to cancel the transaction
     *
     * @param string $tid           The transaction ID in the shop
     * @param int    $shippingCosts The shippingcosts for the transaction multiply by 100
     * @param int    $amount        The amount you want to reserve/bill multiply by 100
     * @param string $cancel        ALLOWED: "true" or "false" - in case of 'true' the transaction will be canceled, otherwise reserved/billed
     * @param string $paymentType   The payment type which will be used for the express checkout (PAYPAL or MASTERPASS)
     *
     * @return PaymentResponse
     */
    public function manualCallback(
        $tid,
        $shippingCosts,
        $amount,
        $cancel,
        $paymentType
    ) // TODO: check if you really want to use the merchant TID and not the mPAY24TID?
    {
        $this->integrityCheck();

        if ($cancel !== "true" && $cancel !== "false") {
            $this->mpay24Sdk->dieWithMsg("The allowed values for the parameter 'cancel' by finishing a PayPal (Express Checkout) payment are 'true' or 'false'!");
        }

        if ($paymentType !== 'PAYPAL' && $paymentType !== 'MASTERPASS') {
            die("The payment type '$paymentType' is not allowed! Allowed are: 'PAYPAL' and 'MASTERPASS'");
        }

        $response = $this->transactionStatusByTid($tid);

        if ($response->hasStatusOk()) {
            $mpay24Tid = $response->transaction['mpaytid'];
        }

        $this->validateTid($tid, $mpay24Tid);
        $this->validateAmount($amount);
        $this->validateShippingCosts($shippingCosts);

        // TODO: implement the logic again
        $order = $this->createFinishExpressCheckoutOrder($transaction, $shippingCosts, $amount, $cancel);

        if (!$order || !$order instanceof MPay24Order) {
            $this->mpay24Sdk->dieWithMsg("To be able to use the Mpay24Api you must create an Mpay24Order object (Mpay24Order.php)!");
        }

        $finishExpressCheckoutResult = $this->mpay24Sdk->manualCallback($order->toXML(), $paymentType);

        $this->recordedLastMessageExchange('FinishExpressCheckoutResult');

        return $finishExpressCheckoutResult;
    }

    /**
     * Return a redirect URL to include in your web page
     *
     * @param string $paymentType The payment type which will be used for the express checkout (CC)
     * @param array  $additional  Additional parameters
     *
     * @return PaymentTokenResponse
     */
    public function createPaymentToken($paymentType, array $additional = [])
    {
        $this->integrityCheck();

        if ($paymentType !== 'CC') {
            die("The payment type '$paymentType' is not allowed! Currently allowed is only: 'CC'");
        }

        $tokenResult = $this->mpay24Sdk->createTokenPayment($paymentType, $additional);

        $this->recordedLastMessageExchange('CreatePaymentToken');

        return $tokenResult;
    }

    /**
     * Clear an amount of an authorized transaction
     *
     * @param string $tid    The transaction ID, for the transaction you want to clear
     * @param int    $amount The amount you want to clear multiply by 100
     *
     * @return Responses\ManagePaymentResponse
     */
    public function manualClear(
        $tid,
        $amount
    ) // TODO: check if you really want to use the merchant TID and not the Mpay24TID?
    {
        $this->integrityCheck();

        $response = $this->transactionStatusByTid($tid);

        if ($response->hasStatusOk()) {
            $mPAYTid  = $response->transaction['mpaytid'];
            $currency = $response->transaction['currency'];
        }

        $this->validateTid($tid, $mPAYTid);
        $this->validateAmount($amount);
        $this->validateCurrency($currency);

        $clearAmountResult = $this->mpay24Sdk->manualClear($mPAYTid, $amount, $currency);

        $this->recordedLastMessageExchange('ClearAmount');

        return $clearAmountResult;
    }

    /**
     * Credit an amount of a billed transaction
     *
     * @param string $tid    The transaction ID, for the transaction you want to credit
     * @param int    $amount The amount you want to credit multiply by 100
     *
     * @return Responses\ManagePaymentResponse
     */
    public function manualCredit(
        $tid,
        $amount
    ) // TODO: check if you really want to use the merchant TID and not the Mpay24TID?
    {
        $this->integrityCheck();

        $response = $this->transactionStatusByTid($tid);

        $mPAYTid  = null;
        $currency = null;
        $customer = null;

        if ($response->hasStatusOk()) {
            $mPAYTid  = $response->transaction['mpaytid'];
            $currency = $response->transaction['currency'];
            $customer = $response->transaction['customer'];
        }

        $this->validateTid($tid, $mPAYTid);
        $this->validateAmount($amount);
        $this->validateCurrency($currency);

        $creditAmountResult = $this->mpay24Sdk->ManualCredit($mPAYTid, $amount, $currency, $customer);

        $this->recordedLastMessageExchange('CreditAmount');

        return $creditAmountResult;
    }

    /**
     * Cancel a authorized transaction
     *
     * @param string $tid The transaction ID, for the transaction you want to cancel
     *
     * @return Responses\ManagePaymentResponse
     */
    public function cancelTransaction($tid
    ) // TODO: check if you really want to use the merchant TID and not the Mpay24TID?
    {
        $this->integrityCheck();

        $response = $this->transactionStatusByTid($tid);

        $mPAYTid = null;

        if ($response->hasStatusOk()) {
            $mPAYTid = $response->transaction['mpaytid'];
        }

        $this->validateTid($tid, $mPAYTid);

        $cancelTransactionResult = $this->mpay24Sdk->manualReverse($mPAYTid);

        $this->recordedLastMessageExchange('CancelTransaction');

        return $cancelTransactionResult;
    }

    protected function integrityCheck()
    {
        if (!$this->mpay24Sdk) {
            die("You are not allowed to define a constructor in the child class of Mpay24!");
        }
    }

    /**
     * @param $messageExchange
     */
    protected function recordedLastMessageExchange($messageExchange)
    {
        if ($this->mpay24Sdk->isDebug()) {
            $this->writeLog($messageExchange, sprintf("REQUEST to %s - %s\n", $this->mpay24Sdk->getEtpURL(),
                str_replace("><", ">\n<", $this->mpay24Sdk->getRequest())));
            $this->writeLog($messageExchange,
                sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mpay24Sdk->getResponse())));
        }
    }

    /**
     * Write a log into a file, file system, data base
     *
     * @param string $operation   The operation, which is to log: GetPaymentMethods, Pay, PayWithProfile, Confirmation, UpdateTransactionStatus, ClearAmount, CreditAmount, CancelTransaction, etc.
     * @param string $info_to_log The information, which is to log: request, response, etc.
     */
    protected function writeLog($operation, $info_to_log)
    {
        $serverName = php_uname('n');

        if (isset($_SERVER['SERVER_NAME'])) {
            $serverName = $_SERVER['SERVER_NAME'];
        }

        $fh = fopen($this->mpay24Sdk->getMpay24LogPath(), 'a+') or die("can't open file");
        $MessageDate = date("Y-m-d H:i:s");
        $Message     = $MessageDate . " " . $serverName . " Mpay24 : ";
        $result      = $Message . "$operation : $info_to_log\n";
        fwrite($fh, $result);
        fclose($fh);
    }

    /**
     * @param null $mpaytid
     * @param null $tid
     *
     * @return Responses\TransactionStatusResponse
     */
    protected function transactionStatus($mpaytid = null, $tid = null)
    {
        $this->integrityCheck();

        $result = $this->mpay24Sdk->transactionStatus($mpaytid, $tid);

        $this->recordedLastMessageExchange('TransactionStatus');

        return $result;
    }

    /**
     * @param $tid
     * @param $mPAYTid
     */
    protected function validateTid($tid, $mPAYTid)
    {
        if (!$mPAYTid) {
            $this->mpay24Sdk->dieWithMsg("The transaction '$tid' you want to cancel with the mPAYTid '$mPAYTid' does not exist in the Mpay24 data base!");
        }
    }

    /**
     * @param $amount
     */
    protected function validateAmount($amount)
    {
        if (!$amount || !is_numeric($amount)) {
            $this->mpay24Sdk->dieWithMsg("The amount '$amount' you are trying to credit is not valid!");
        }
    }

    /**
     * @param $currency
     */
    protected function validateCurrency($currency)
    {
        if (!$currency || strlen($currency) != 3) {
            $this->mpay24Sdk->dieWithMsg("The currency code '$currency' for the amount you are trying to clear is not valid (3-digit ISO-Currency-Code)!");
        }
    }

    /**
     * @param $shippingCosts
     */
    protected function validateShippingCosts($shippingCosts)
    {
        if (!$shippingCosts || !is_numeric($shippingCosts)) {
            $this->mpay24Sdk->dieWithMsg("The shipping costs '$shippingCosts' you are trying to set are not valid!");
        }
    }
}

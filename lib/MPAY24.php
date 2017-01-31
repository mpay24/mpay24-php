<?php

namespace mPay24;

use mPay24\Responses\PaymentResponse;
use mPay24\Responses\PaymentTokenResponse;
use mPay24\Responses\ListPaymentMethodsResponse;

/**
 * The abstract MPAY24 class provides abstract functions, which are used from the other functions in order to make a payment or a request to mPAY24
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @filesource MPAY24.php
 * @license MIT
 */
class MPAY24
{
    /**
     * @var MPAY24SDK|null
     */
    var $mPAY24SDK = null;

    /**
     * MPAY24 constructor.
     */
	public function __construct()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_a($args[0], MPay24Config::class ))
        {
            $config = $args[0];
        }
        else
        {
            $config = new MPay24Config($args);
        }

        $this->mPAY24SDK = new MPAY24SDK($config);

        $this->mPAY24SDK->checkRequirements(true, true, false);
    }

    /**
     * Get a list which includes all the payment methods (activated by mPAY24) for your mechant ID
     *
     * @return ListPaymentMethodsResponse
     */
	public function listPaymentMethods()
    {
	    $this->integrityCheck();

	    $paymentMethods = $this->mPAY24SDK->ListPaymentMethods();

	    $this->recordedLastMessageExchange("GetPaymentMethods");

	    return $paymentMethods;
    }

    /**
     * Return a redirect URL to start a payment
     *
     * @param $mdxi
     * @return PaymentResponse
     */
	public function selectPayment( $mdxi )
    {
	    $this->integrityCheck();

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

        $this->recordedLastMessageExchange('SelectPayment');

        return $payResult;
    }

    /**
     * Start a backend to backend payment
     *
     * @param string $paymentType The payment type which will be used for the payment (EPS, SOFORT, PAYPAL, MASTERPASS or TOKEN)
     * @param $tid
     * @param $payment
     * @param $additional
     * @return PaymentResponse
     */
	public function acceptPayment( $paymentType, $tid, $payment, $additional )
	{
		$this->integrityCheck();
		$payBackend2BackendResult = $this->mPAY24SDK->AcceptPayment($paymentType, $tid, $payment, $additional);
		$this->recordedLastMessageExchange('AcceptPayment');
        return $payBackend2BackendResult;
    }

	/**
	 * Get the status for a transaction by the unique mPAYTID number
	 *
	 * @param string $mpaytid
	 * @return Responses\TransactionStatusResponse
	 */
	public function transactionStatusByMPayID( $mpaytid )
	{
		return $this->transactionStatus($mpaytid);
	}

	/**
	 * Get the status for the last transaction with the given merchant TID number
	 *
	 * @param string $tid
	 * @return Responses\TransactionStatusResponse
	 */
	public function transactionStatusByTID( $tid )
	{
		return $this->transactionStatus(null, $tid);
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
	public function manualCallback( $tid, $shippingCosts, $amount, $cancel, $paymentType )
    {
	    $this->integrityCheck();

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

        $this->recordedLastMessageExchange('FinishExpressCheckoutResult');

        return $finishExpressCheckoutResult;
    }

    /**
     * Return a redirect URL to include in your web page
     *
     * @param string $paymentType The payment type which will be used for the express checkout (CC)
     * @param array $additional Additional parameters
     * @return PaymentTokenResponse
     */
    public function createPaymentToken( $paymentType, array $additional = [] )
    {
	    $this->integrityCheck();

        if ( $paymentType !== 'CC' ) {
            die("The payment type '$paymentType' is not allowed! Currently allowed is only: 'CC'");
        }

        $tokenResult = $this->mPAY24SDK->CreateTokenPayment($paymentType, $additional);

        $this->recordedLastMessageExchange('CreatePaymentToken');

        return $tokenResult;
    }

    /**
     * Clear an amount of an authorized transaction
     *
     * @param string $tid The transaction ID, for the transaction you want to clear
     * @param int $amount The amount you want to clear multiply by 100
     * @return Responses\ManagePaymentResponse
     */
	public function manualClear( $tid, $amount )
    {
	    $this->integrityCheck();

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

        $this->recordedLastMessageExchange('ClearAmount');

        return $clearAmountResult;
    }

    /**
     * Credit an amount of a billed transaction
     *
     * @param string $tid The transaction ID, for the transaction you want to credit
     * @param int $amount The amount you want to credit multiply by 100
     * @return Responses\ManagePaymentResponse
     */
	public function manualCredit( $tid, $amount )
    {
	    $this->integrityCheck();

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

        $this->recordedLastMessageExchange('CreditAmount');

        return $creditAmountResult;
    }

    /**
     * Cancel a authorized transaction
     *
     * @param string $tid The transaction ID, for the transaction you want to cancel
     * @return Responses\ManagePaymentResponse
     */
	public function cancelTransaction( $tid )
    {
	    $this->integrityCheck();

        $mPAYTid = $transaction->MPAYTID; // ToDo: again...find from where this came from..

        if (!$mPAYTid) {
            $this->mPAY24SDK->dieWithMsg("The transaction '$tid' you want to cancel with the mPAYTid '$mPAYTid' does not exist in the mPAY24 data base!");
        }

        $cancelTransactionResult = $this->mPAY24SDK->ManualReverse($mPAYTid);

        $this->recordedLastMessageExchange('CancelTransaction');

        return $cancelTransactionResult;
    }

	protected function integrityCheck()
	{
		if (!$this->mPAY24SDK)
		{
			die("You are not allowed to define a constructor in the child class of MPAY24!");
		}
	}

	/**
	 * @param $messageExchange
	 */
	protected function recordedLastMessageExchange($messageExchange)
	{
		if ($this->mPAY24SDK->isDebug())
		{
			$this->writeLog($messageExchange, sprintf("REQUEST to %s - %s\n", $this->mPAY24SDK->getEtpURL(), str_replace("><", ">\n<", $this->mPAY24SDK->getRequest())));
			$this->writeLog($messageExchange, sprintf("RESPONSE - %s\n", str_replace("><", ">\n<", $this->mPAY24SDK->getResponse())));
		}
	}

	/**
	 * Write a log into a file, file system, data base
	 *
	 * @param string $operation The operation, which is to log: GetPaymentMethods, Pay, PayWithProfile, Confirmation, UpdateTransactionStatus, ClearAmount, CreditAmount, CancelTransaction, etc.
	 * @param string $info_to_log The information, which is to log: request, response, etc.
	 */
	protected function writeLog($operation, $info_to_log )
	{
		$serverName = php_uname('n');

		if (isset($_SERVER['SERVER_NAME']))
		{
			$serverName = $_SERVER['SERVER_NAME'];
		}

		$fh = fopen($this->mPAY24SDK->getMPay24LogPath(), 'a+') or die("can't open file");
		$MessageDate = date("Y-m-d H:i:s");
		$Message = $MessageDate." ".$serverName." mPAY24 : ";
		$result = $Message."$operation : $info_to_log\n";
		fwrite($fh, $result);
		fclose($fh);
	}

	/**
	 * @param null $mpaytid
	 * @param null $tid
	 * @return Responses\TransactionStatusResponse
	 */
	protected function transactionStatus( $mpaytid = null, $tid = null )
	{
		$this->integrityCheck();

		$result = $this->mPAY24SDK->TransactionStatus($mpaytid, $tid);

		$this->recordedLastMessageExchange('TransactionStatus');

		return $result;
	}
}

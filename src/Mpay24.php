<?php

namespace Mpay24;

/**
 * The Mpay24 class provides functions, which are used to make a payment or a request to mPAY24
 *
 * Class Mpay24
 * @package    Mpay24
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource Mpay24.php
 * @license    MIT
 */
class Mpay24
{
    /**
     * @var Mpay24Sdk|null
     */
    var $mpay24Sdk = null;

    /**
     * Mpay24 constructor.
     */
    public function __construct()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_a($args[0], 'Mpay24\Mpay24Config')) {
            $config = $args[0];
        } else {
            $config = new Mpay24Config($args);
        }

        $this->mpay24Sdk = new Mpay24Sdk($config);

        $this->mpay24Sdk->checkRequirements(true, true);
    }

    /**
     * Get a list which includes all the payment methods (activated by mPAY24) for your mechant ID
     *
     * @return Responses\ListPaymentMethodsResponse
     */
    public function paymentMethods()
    {
        $this->integrityCheck();

        $paymentMethods = $this->mpay24Sdk->listPaymentMethods();

        $this->recordedLastMessageExchange("PaymentMethods");

        return $paymentMethods;
    }

    /**
     * Return a redirect URL to start a payment
     *
     * @param $mdxi
     *
     * @return Responses\SelectPaymentResponse
     */
    public function paymentPage($mdxi)
    {
        $this->integrityCheck();

        libxml_use_internal_errors(true);

        if (!$mdxi || !$mdxi instanceof Mpay24Order) {
            $this->mpay24Sdk->dieWithMsg("To be able to use the Mpay24Api you must create an Mpay24Order object (Mpay24Order.php) and fulfill it with a MDXI!");
        }

        $mdxiXML = $mdxi->toXML();

        $payResult = $this->mpay24Sdk->selectPayment($mdxiXML);

        $this->recordedLastMessageExchange('PaymentPage');

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
     * @return Responses\AcceptPaymentResponse
     */
    public function payment($paymentType, $tid, $payment, $additional)
    {
        $this->integrityCheck();
        $payBackend2BackendResult = $this->mpay24Sdk->acceptPayment($paymentType, $tid, $payment, $additional);
        $this->recordedLastMessageExchange('Payment');

        return $payBackend2BackendResult;
    }

    /**
     * Get the status for a transaction by the unique mPAYTID
     *
     * @param string $mpayTid
     *
     * @return Responses\TransactionStatusResponse
     */
    public function paymentStatus($mpayTid)
    {
        return $this->transactionStatus($mpayTid);
    }

    /**
     * Get the status for the last transaction with the given merchant TID
     *
     * @param string $tid
     *
     * @return Responses\TransactionStatusResponse
     */
    public function paymentStatusByTid($tid)
    {
        return $this->transactionStatus(null, $tid);
    }

    /**
     * Get all transaction's states for specified mPAYTID
     *
     * @param string $mpayTid
     *
     * @return Responses\TransactionHistoryResponse
     */
    public function paymentHistory($mpayTid)
    {
        $this->integrityCheck();

        $response = $this->mpay24Sdk->transactionHistory($mpayTid);

        $this->recordedLastMessageExchange('TransactionHistory');

        return $response;
    }

    /**
     * Get all profile according to the given parameters
     *
     * @param string $customerId
     * @param string $expiredBy
     * @param int    $begin
     * @param int    $size
     *
     * @return Responses\ListProfilesResponse
     * @internal param string $mpayTid
     *
     */
    public function listCustomers($customerId = null, $expiredBy = null, $begin = null, $size = null)
    {
        $this->integrityCheck();

        $response = $this->mpay24Sdk->listProfiles($customerId, $expiredBy, $begin, $size);

        $this->recordedLastMessageExchange('ListProfiles');

        return $response;
    }

    /**
     * Return a redirect URL to include in your web page
     *
     * @param string $paymentType The payment type which will be used for the express checkout (CC)
     * @param array  $additional  Additional parameters
     *
     * @return Responses\CreatePaymentTokenResponse
     */
    public function token($paymentType, array $additional = [])
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
     * Capture an amount of an authorized transaction
     *
     * @param string $mpayTid The transaction ID, for the transaction you want to clear
     * @param int    $amount  The amount you want to clear multiply by 100
     *
     * @return Responses\ManualClearResponse
     */
    public function capture($mpayTid, $amount)
    {
        $this->integrityCheck();

        $this->validateAmount($amount);

        $clearAmountResult = $this->mpay24Sdk->manualClear($mpayTid, $amount);

        $this->recordedLastMessageExchange('CaptureAmount');

        return $clearAmountResult;
    }

    /**
     * Refund an amount of a captured transaction
     *
     * @param string $mpayTid The transaction ID, for the transaction you want to credit
     * @param int    $amount  The amount you want to credit multiply by 100
     *
     * @return Responses\ManualCreditResponse
     */
    public function refund($mpayTid, $amount)
    {
        $this->integrityCheck();

        $this->validateAmount($amount);

        $creditAmountResult = $this->mpay24Sdk->manualCredit($mpayTid, $amount);

        $this->recordedLastMessageExchange('RefundAmount');

        return $creditAmountResult;
    }

    /**
     * Cancel a authorized transaction
     *
     * @param string $mpayTid The transaction ID, for the transaction you want to cancel
     *
     * @return Responses\AbstractTransactionResponse
     */
    public function cancel($mpayTid)
    {
        $this->integrityCheck();

        $cancelTransactionResult = $this->mpay24Sdk->manualReverse($mpayTid);

        $this->recordedLastMessageExchange('CancelTransaction');

        return $cancelTransactionResult;
    }

    /**
     * Create a customer for recurring payments
     *
     * @param string $paymentType The payment type which will be used for the payment (CC or TOKEN)
     * @param        $customerId
     * @param        $payment
     * @param        $additional
     *
     * @return Responses\CreateCustomerResponse
     */
    public function createCustomer($paymentType, $customerId, $payment, $additional = [])
    {
        $this->integrityCheck();
        $createCustomerRes = $this->mpay24Sdk->createCustomer($paymentType, $customerId, $payment, $additional);
        $this->recordedLastMessageExchange('CreateCustomer');

        return $createCustomerRes;
    }

    /**
     * Delete a profile.
     *
     * @param string      $customerId
     * @param string|null $profileId
     *
     * @return Responses\DeleteProfileResponse
     */
    public function deleteProfile($customerId, $profileId = null)
    {
        $this->integrityCheck();

        $response = $this->mpay24Sdk->deleteProfile($customerId, $profileId);
        $this->recordedLastMessageExchange('DeleteProfile');
        return $response;
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
     * @param null $mpayTid
     * @param null $tid
     *
     * @return Responses\TransactionStatusResponse
     */
    protected function transactionStatus($mpayTid = null, $tid = null)
    {
        $this->integrityCheck();

        $result = $this->mpay24Sdk->transactionStatus($mpayTid, $tid);

        $this->recordedLastMessageExchange('TransactionStatus');

        return $result;
    }

    /**
     * @param $tid
     * @param $mpayTid
     */
    protected function validateTid($tid, $mpayTid)
    {
        if (!$mpayTid) {
            $this->mpay24Sdk->dieWithMsg("The transaction '$tid' you send us could not assigned to a unique mPAYTID and maybe does not exist in the mPAY24 data base!");
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

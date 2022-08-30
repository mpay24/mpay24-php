<?php

namespace Mpay24;

use Exception;
use Mpay24\Exception\CanNotOpenFileException;
use Mpay24\Exception\InvalidArgumentException;
use Mpay24\Exception\RequirementException;
use Mpay24\Requests\AcceptPayment;
use Mpay24\Requests\CreateCustomer;
use Mpay24\Requests\CreatePaymentToken;
use Mpay24\Requests\CreateApplePayToken;
use Mpay24\Requests\CreateGooglePayToken;
use Mpay24\Requests\DeleteProfile;
use Mpay24\Requests\ListPaymentMethods;
use Mpay24\Requests\ListProfiles;
use Mpay24\Requests\ManualCallback;
use Mpay24\Requests\ManualClear;
use Mpay24\Requests\ManualCredit;
use Mpay24\Requests\ManualReverse;
use Mpay24\Requests\SelectPayment;
use Mpay24\Requests\TransactionHistory;
use Mpay24\Requests\TransactionStatus;
use Mpay24\Responses\AcceptPaymentResponse;
use Mpay24\Responses\CreateCustomerResponse;
use Mpay24\Responses\CreatePaymentTokenResponse;
use Mpay24\Responses\CreateApplePayTokenResponse;
use Mpay24\Responses\CreateGooglePayTokenResponse;
use Mpay24\Responses\DeleteProfileResponse;
use Mpay24\Responses\ListPaymentMethodsResponse;
use Mpay24\Responses\ListProfilesResponse;
use Mpay24\Responses\ManualCallbackResponse;
use Mpay24\Responses\ManualClearResponse;
use Mpay24\Responses\ManualCreditResponse;
use Mpay24\Responses\ManualReverseResponse;
use Mpay24\Responses\SelectPaymentResponse;
use Mpay24\Responses\TransactionHistoryResponse;
use Mpay24\Responses\TransactionStatusResponse;

/**
 * Main Mpay24 PHP APIs Class.
 *
 * The Mpay24Sdk class provides the communication functioanallity.
 * It hold's all the sensitive data (merchant ID, SOAP password, etc) and build the SOAP request, sent to mPAY24.
 *
 * Class Mpay24Sdk
 * @package    Mpay24
 *
 * @author     Unzer Austria GmbH <online.support.at@unzer.com>
 * @author     Stefan Polzer <develop@ps-webdesign.com>, Milko Daskalov <milko.daskalov@unzer.com>
 * @filesource Mpay24SDK.php
 * @license    MIT
 */
class Mpay24Sdk
{
    /**
     * An error message, that will be displayed to the user in case you are using the LIVE system
     * @const LIVE_ERROR_MSG
     */
    const LIVE_ERROR_MSG = "We are sorry, an error occurred - please contact the merchant!";

    /**
     * The link where the requests should be sent to if you use the
     *
     * TEST SYSTEM : https://test.mpay24.com/app/bin/etpproxy_v15
     *
     * @const string
     *
     */
    const ETP_TEST_URL = "https://test.mpay24.com/app/bin/etpproxy_v15";

    /**
     * The link where the requests should be sent to if you use the
     *
     * LIVE SYSTEM : https://www.mpay24.com/app/bin/etpproxy_v15
     *
     * @const string
     *
     */
    const ETP_LIVE_URL = "https://www.mpay24.com/app/bin/etpproxy_v15";

    /**
     * User Agent Version Number
     *
     * @const string
     */
    const VERSION = "5.1.0";

    /**
     * Minimum PHP version Required
     *
     * @const string
     */
    const MIN_PHP_VERSION = "7.2";

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
     * @var Mpay24Config
     */
    protected $config;

    public function __construct(Mpay24Config &$config = null)
    {
        $this->config = is_null($config) ? new Mpay24Config() : $config;
    }

    /**
     * @param bool $checkDomExtension
     * @param bool $checkCurlExtension
     *
     * @throws RequirementException
     */
    public function checkRequirements(
        $checkDomExtension = true,
        $checkCurlExtension = true
    ) {
        if (version_compare(phpversion(), self::MIN_PHP_VERSION, '<') === true
            || ($checkCurlExtension && !in_array('curl', get_loaded_extensions()))
            || ($checkDomExtension && !in_array('dom', get_loaded_extensions()))
        ) {
            if (version_compare(phpversion(), self::MIN_PHP_VERSION, '<') === true) {
                throw new RequirementException('You need PHP version ' . self::MIN_PHP_VERSION . ' or newer!');
            }

            if ($checkCurlExtension && !in_array('curl', get_loaded_extensions())) {
                throw new RequirementException('You need cURL extension!');
            }

            if ($checkDomExtension && !in_array('dom', get_loaded_extensions())) {
                throw new RequirementException("You need DOM extension!");
            }
        }
    }

    /**
     * Get the merchant ID, which was set by the function configure(Config $config)
     *
     * @return string
     */
    public function getMerchantID()
    {
        return $this->config->getMerchantId();
    }

    /**
     * Get the url, where requests are going to be posted
     *
     * @return string
     */
    public function getEtpURL()
    {
        return $this->config->isTestSystem() ? self::ETP_TEST_URL : self::ETP_LIVE_URL;
    }

    /**
     * Get the request, which was sent to mPAY24 (in XML form)
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the response from mPAY24 (in XML form)
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Check whether a proxy is used
     *
     * @return bool
     */
    public function proxyUsed()
    {
        return $this->config->getProxyHost() != '';
    }

    /**
     * Set debug mode (FALSE by default)
     *
     * @param bool $debug TRUE if is turned on, otherwise FALSE
     */
    public function setDebug($debug)
    {
        $this->config->setDebug((bool)$debug);
    }

    /**
     * Check whether the debug modus is turned on or off
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->config->isDebug();
    }

    /**
     * Return mPAY24 Log Path
     *
     * @return string
     */
    public function getMpay24LogPath()
    {
        return $this->config->getLogPath() . '/' . $this->config->getLogFile();
    }

    /**
     * Return mPAY24 Curl Log Path
     *
     * @return string
     */
    public function getMpay24CurlLogPath()
    {
        return $this->config->getLogPath() . '/' . $this->config->getCurlLogFile();
    }

    /**
     * @param string $message The message, which is shown to the user
     *
     * @throws InvalidArgumentException
     */
    public function invalidArgument($message)
    {
        $message = $this->config->isTestSystem() ? $message : '';

        throw new InvalidArgumentException($message);
    }

    /**
     * In case the test system is used, show die with the real error message, otherwise, show the defined constant error LIVE_ERROR_MSG
     *
     * @param string $msg The message, which is shown to the user
     *
     * @throws \Exception
     *
     * @deprecated 5.0.0
     */
    public function dieWithMsg($msg)
    {
        $msg = $this->config->isTestSystem() ? $msg : self::LIVE_ERROR_MSG;

        throw new \Exception($msg);
    }

    /**
     * In case the test system is used, show print the real error message, otherwise, show the defined constant error LIVE_ERROR_MSG
     *
     * @param string $msg The message, which is shown to the user
     *
     * @deprecated 5.0.0
     */
    public function printMsg($msg)
    {
        $msg = $this->config->isTestSystem() ? $msg : self::LIVE_ERROR_MSG;

        print($msg);
    }

    /**
     * Die with an error message, which show the path in case of read/write permission errors
     */
    public function permissionError()
    {
        $errors  = error_get_last();
        $message = $errors['message'];
        $path    = substr(
            $message,
            strpos($message, 'fopen(') + 6,
            strpos($message, ')') - (strpos($message, 'fopen(') + 6)
        );

        throw new CanNotOpenFileException($path);
    }

    /**
     * Get all the payment methods, that are available for the merchant by mPAY24
     *
     * @return ListPaymentMethodsResponse
     */
    public function listPaymentMethods()
    {
        $request = new ListPaymentMethods($this->config->getMerchantId());

        $this->request = $request->getXml();

        $this->send();

        $result = new ListPaymentMethodsResponse($this->response);

        return $result;
    }

    /**
     * Start a secure payment through the mPAY24 payment window -
     * the sensible data (credit card numbers, bank account numbers etc)
     * is (will be) not saved in the shop
     *
     * @param string $mdxi The mdxi xml, which contains the shopping cart
     *
     * @return SelectPaymentResponse
     */
    public function selectPayment($mdxi)
    {
        $request = new SelectPayment($this->config->getMerchantId());

        $request->setMdxi($mdxi);

        $this->request = $request->getXml();

        $this->send();

        $result = new SelectPaymentResponse($this->response);

        return $result;
    }

    /**
     * Start a secure payment using the mPAY24 Tokenizer.
     *
     * @param string $pType The payment type used for the tokenization (currently supported 'CC')
     * @param array  $additional
     *
     * @return CreatePaymentTokenResponse
     */
    public function createTokenPayment($pType, array $additional = [])
    {
        $request = new CreatePaymentToken($this->config->getMerchantId());

        $request->setPType($pType);
        $request->setAdditional($additional);

        $this->request = $request->getXml();

        $this->send();

        $result = new CreatePaymentTokenResponse($this->response);

        return $result;
    }

    /**
     * Start Apple Pay payment integraged into your web page
     *
     * @param integer $amount Total payment amount shown to the customer
     * @param string $currency Currency used for the payment
     * @param string $domain Web page domain where Apple Pay will be integrated (https://www.yourdomain.com)
     * @param string $language Language used for the Apple Pay session
     *
     * @return CreateApplePayTokenResponse
     */
    public function createApplePayPayment($amount, $currency, $domain = null, $language = null)
    {
        $request = new CreateApplePayToken($this->config->getMerchantId());

        $request->setAmount($amount);
        $request->setCurrency($currency);
        $request->setDomain($domain);
        $request->setLanguage($language);

        $this->request = $request->getXml();

        $this->send();

        $result = new CreateApplePayTokenResponse($this->response);

        return $result;
    }

    /**
     * Start Google Pay payment integraged into your web page
     *
     * @param integer $amount Total payment amount shown to the customer
     * @param string $currency Currency used for the payment
     * @param string $language Language used for the Google Pay session
     *
     * @return CreateGooglePayTokenResponse
     */
    public function createGooglePayPayment($amount, $currency, $language = null)
    {
        $request = new CreateGooglePayToken($this->config->getMerchantId());

        $request->setAmount($amount);
        $request->setCurrency($currency);
        $request->setLanguage($language);

        $this->request = $request->getXml();

        $this->send();

        $result = new CreateGooglePayTokenResponse($this->response);

        return $result;
    }

    /**
     * Initialize a manual callback to mPAY24 in order to check the information provided by PayPal
     *
     * @param string $type
     * @param string $tid The TID used for the transaction
     * @param array  $payment
     * @param array  $additional
     *
     * @return AcceptPaymentResponse
     */
    public function acceptPayment($type, $tid, $payment = [], $additional = [])
    {
        $request = new AcceptPayment($this->config->getMerchantId());

        $request->setPType($type);
        $request->setTid($tid);
        $request->setPayment($payment);
        $request->setAdditional($additional);

        $this->request = $request->getXml();

        $this->send();

        $result = new AcceptPaymentResponse($this->response);

        return $result;
    }

    /**
     * Initialize a manual callback to mPAY24 in order to check the information provided by PayPal
     *
     * @param integer $mpayTid
     * @param string  $paymentType The payment type which will be used for the express checkout (PAYPAL or MASTERPASS)
     *
     * @param integer $amount
     * @param bool    $cancel
     * @param null    $order
     *
     * @return ManualCallbackResponse
     * @internal param string $requestString The callback request to mPAY24
     */
    public function manualCallback($mpayTid, $paymentType, $amount = null, $cancel = false, $order = null)
    {
        $request = new ManualCallback($this->config->getMerchantId());

        $request->setMpayTid($mpayTid);
        $request->setType($paymentType);
        $request->setAmount($amount);
        $request->setCancel($cancel);
        $request->setOrder($order);

        $this->request = $request->getXml();

        $this->send();

        $result = new ManualCallbackResponse($this->response);

        return $result;
    }

    /**
     * Clear a transaction with an amount
     *
     * @param integer $mpayTid The mPAY24 transaction ID
     * @param integer $amount  The amount to be cleared multiplay by 100
     *
     * @return ManualClearResponse
     */
    public function manualClear($mpayTid, $amount)
    {
        $request = new ManualClear($this->config->getMerchantId());

        $request->setMpayTid($mpayTid);
        $request->setAmount($amount);

        $this->request = $request->getXml();

        $this->send();

        $result = new ManualClearResponse($this->response);

        return $result;
    }

    /**
     * Credit a transaction with an amount
     *
     * @param integer $mpayTid The mPAY24 transaction ID
     * @param integer $amount  The amount to be credited multiplay by 100
     *
     * @return ManualCreditResponse
     */
    public function manualCredit($mpayTid, $amount)
    {
        $request = new ManualCredit($this->config->getMerchantId());

        $request->setMpayTid($mpayTid);
        $request->setAmount($amount);

        $this->request = $request->getXml();

        $this->send();

        $result = new ManualCreditResponse($this->response);

        return $result;
    }

    /**
     * Cancel a transaction
     *
     * @param integer $mpayTid The mPAY24 transaction ID for the transaction you want to cancel
     *
     * @return ManualReverseResponse
     */
    public function manualReverse($mpayTid)
    {
        $request = new ManualReverse($this->config->getMerchantId());

        $request->setMpayTid($mpayTid);

        $this->request = $request->getXml();

        $this->send();

        $result = new ManualReverseResponse($this->response);

        return $result;
    }

    /**
     * Get all the information for a transaction, supported by mPAY24
     *
     * @param integer $mpay24tid The mPAY24 transaction ID
     * @param string  $tid       The transaction ID from your shop
     *
     * @return TransactionStatusResponse
     */
    public function transactionStatus($mpay24tid = null, $tid = null)
    {
        $request = new TransactionStatus($this->config->getMerchantId());

        $request->setMpayTid($mpay24tid);
        $request->setTid($tid);

        $this->request = $request->getXml();

        $this->send();

        $result = new TransactionStatusResponse($this->response);

        return $result;
    }

    /**
     * Get all the information for a transaction, supported by mPAY24
     *
     * @param integer $mpayTid The mPAY24 transaction ID
     *
     * @return TransactionHistoryResponse
     */
    public function transactionHistory($mpayTid = null)
    {
        $request = new TransactionHistory($this->config->getMerchantId());

        $request->setMpayTid($mpayTid);

        $this->request = $request->getXml();

        $this->send();

        $result = new TransactionHistoryResponse($this->response);

        return $result;
    }

    /**
     * Get all the information for a transaction, supported by mPAY24
     *
     * @param string  $customerId
     * @param string  $expiredBy
     * @param integer $begin
     * @param integer $size
     *
     * @return ListProfilesResponse
     * @internal param int $mpay24tid The mPAY24 transaction ID
     *
     */
    public function listProfiles($customerId = null, $expiredBy = null, $begin = null, $size = null)
    {
        $request = new ListProfiles($this->config->getMerchantId());

        $request->setCustomerId($customerId);
        $request->setExpiredBy($expiredBy);
        $request->setBegin($begin);
        $request->setSize($size);

        $this->request = $request->getXml();

        $this->send();

        $result = new ListProfilesResponse($this->response);

        return $result;
    }

    /**
     * Create a new customer for recurring payments
     *
     * @param string     $type
     * @param string     $customerId
     * @param array|null $payment
     * @param array|null $additional
     *
     * @return CreateCustomerResponse
     */
    public function createCustomer($type, $customerId, $payment = [], $additional = [])
    {
        $request = new CreateCustomer($this->config->getMerchantId());

        $request->setPType($type);
        $request->setPaymentData($payment);
        $request->setCustomerId($customerId);
        $request->setAdditional($additional);

        $this->request = $request->getXml();

        $this->send();

        $result = new CreateCustomerResponse($this->response);

        return $result;
    }

    /**
     * Deletes a profile.
     *
     * @param string      $customerId
     * @param string|null $profileId
     *
     * @return DeleteProfileResponse
     */
    public function deleteProfile($customerId, $profileId = null)
    {
        $request = new DeleteProfile($this->config->getMerchantId());
        $request->setCustomerId($customerId);
        $request->setProfileId($profileId);

        $this->request = $request->getXml();
        $this->send();

        $result = new DeleteProfileResponse($this->response);

        return $result;
    }

    /**
     * Create a curl request and send the created SOAP XML
     */
    protected function send()
    {
        $userAgent = 'mpay24-php/' . self::VERSION;

        $ch = curl_init($this->getEtpURL());
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD,
            'u' . $this->config->getMerchantId() . ':' . $this->config->getSoapPassword());
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($this->config->isEnableCurlLog()) {
            $fh = fopen($this->getMpay24CurlLogPath(), 'a+') or $this->permissionError();

            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_STDERR, $fh);
        }

        try {
            curl_setopt($ch, CURLOPT_CAINFO, $this->config->getCaCertPath() . $this->config->getCaCertFileName());

            if ($this->config->getProxyHost()) {
                curl_setopt($ch, CURLOPT_PROXY, $this->config->getProxyHost() . ':' . $this->config->getProxyPort());

                if ($this->config->getProxyUser()) {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD,
                        $this->config->getProxyUser() . ':' . $this->config->getProxyPass());
                }

                if ($this->config->isVerifyPeer() !== true) {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->config->isVerifyPeer());
                }
            }

            $this->response = curl_exec($ch);
            curl_close($ch);

        } catch (Exception $exception) {
            $message = $this->config->isTestSystem()
                ? "Your request couldn't be sent because of the following error:" . "\n" . curl_error($ch) . "\n"
                . $exception->getMessage() . ' in ' . $exception->getFile() . ', line: ' . $exception->getLine() . '.'
                : self::LIVE_ERROR_MSG;

            echo $message;
        }

        if (isset($fh) && $this->config->isEnableCurlLog()) {
            fclose($fh);
        }
    }
}

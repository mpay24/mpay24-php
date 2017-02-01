<?php

namespace Mpay24;

use DOMDocument;
use DOMNode;
use Mpay24\Responses\ListPaymentMethodsResponse;
use Mpay24\Responses\ManagePaymentResponse;
use Mpay24\Responses\PaymentResponse;
use Mpay24\Responses\PaymentTokenResponse;
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
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource Mpay24SDK.php
 * @license    MIT
 */
class Mpay24Sdk
{
    /**
     * An error message, that will be displayed to the user in case you are using the LIVE system
     * @const LIVE_ERROR_MSG
     */
    const LIVE_ERROR_MSG = "We are sorry, an error occured - please contact the merchant!";

    /**
     * The link where the requests should be sent to if you use the
     *
     * TEST SYSTEM : https://test.Mpay24.com/app/bin/etpproxy_v15
     *
     * @const string
     *
     */
    const ETP_TEST_URL = "https://test.Mpay24.com/app/bin/etpproxy_v15";

    /**
     * The link where the requests should be sent to if you use the
     *
     * LIVE SYSTEM : https://www.Mpay24.com/app/bin/etpproxy_v15
     *
     * @const string
     *
     */
    const ETP_LIVE_URL = "https://www.Mpay24.com/app/bin/etpproxy_v15";

    /**
     * User Agent Version Number
     *
     * @const string
     */
    const VERSION = "4.0.0";

    /**
     * Minimum PHP version Required
     *
     * @const string
     */
    const MIN_PHP_VERSION = "5.3.3";

    /**
     * The whole soap-xml (envelope and body), which is to be sent to Mpay24 as request
     *
     * @var string
     */
    private $request = "";

    /**
     * The response from Mpay24
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
        if (is_null($config)) {
            $config = new Mpay24Config();
        }

        $this->config = $config;
    }

    /**
     * @param bool $checkDomExtension
     * @param bool $checkCurlExtension
     * @param bool $checkMCryptExtension
     */
    public function checkRequirements(
        $checkDomExtension = true,
        $checkCurlExtension = true,
        $checkMCryptExtension = true
    ) {
        if (version_compare(phpversion(), self::MIN_PHP_VERSION, '<') === true
            || ($checkCurlExtension && !in_array('curl', get_loaded_extensions()))
            || ($checkDomExtension && !in_array('dom', get_loaded_extensions()))
            || ($checkMCryptExtension && !in_array('mcrypt', get_loaded_extensions()))
        ) {
            $this->printMsg("ERROR: You don't meet the needed requirements for this example shop.<br>");

            if (version_compare(phpversion(), self::MIN_PHP_VERSION, '<') === true) {
                $this->printMsg('You need PHP version ' . self::MIN_PHP_VERSION . ' or newer!<br>');
            }

            if ($checkCurlExtension && !in_array('curl', get_loaded_extensions())) {
                $this->printMsg("You need cURL extension!<br>");
            }

            if ($checkDomExtension && !in_array('dom', get_loaded_extensions())) {
                $this->printMsg("You need DOM extension!<br>");
            }

            if ($checkMCryptExtension && !in_array('mcrypt', get_loaded_extensions())) {
                $this->printMsg("You need mcrypt extension!<br>");
            }

            $this->dieWithMsg("Please load the required extensions!");
        }
    }

    /**
     * Set the basic (mandatory) settings for the requests
     *
     * @param string $spid
     *          The SPID of your account, supported by Mpay24
     * @param string $password
     *          The flexLINK password, supported by Mpay24
     * @param bool   $test
     *          TRUE - when you want to use the TEST system
     *
     *          FALSE - when you want to use the LIVE system
     *
     * @deprecated Use Configuration Object instated
     */
    public function configureFlexLINK($spid, $password, $test)
    {
        $this->config->setSpid($spid);
        $this->config->setFlexLinkPassword($password);
        $this->config->useFlexLinkTestSystem($test);
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
     * Get the SPID, which was set by the function configureFlexLINK($spid, $password, $test)
     *
     * @return string
     */
    public function getSpid()
    {
        return $this->config->getSPID();
    }

    /**
     * Get the system, which should be used for flexLINK (test -> 'test' or live -> 'www')
     *
     * @return string
     */
    public function getFlexLinkSystem()
    {
        return $this->config->isFlexLinkTestSystem() ? 'test' : 'www';
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
     * Get the request, which was sent to Mpay24 (in XML form)
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the response from Mpay24 (in XML form)
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
     * Return Mpay24 Log Path
     *
     * @return string
     */
    public function getMpay24LogPath()
    {
        return $this->config->getLogPath() . '/' . $this->config->getLogFile();
    }

    /**
     * Return Mpay24 Curl Log Path
     *
     * @return string
     */
    public function getMpay24CurlLogPath()
    {
        return $this->config->getLogPath() . '/' . $this->config->getCurlLogFile();
    }

    /**
     * In case the test system is used, show die with the real error message, otherwise, show the defined constant error LIVE_ERROR_MSG
     *
     * @param string $msg The message, which is shown to the user
     *
     * @throws \Exception
     */
    public function dieWithMsg($msg)
    {
        if ($this->config->isTestSystem()) {
            throw new \Exception($msg);
        } else {
            throw new \Exception();
        }
    }

    /**
     * In case the test system is used, show print the real error message, otherwise, show the defined constant error LIVE_ERROR_MSG
     *
     * @param string $msg The message, which is shown to the user
     */
    public function printMsg($msg)
    {
        if ($this->config->isTestSystem()) {
            print($msg);
        } else {
            print(self::LIVE_ERROR_MSG);
        }
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
        $this->dieWithMsg("Can't open file '$path'! Please set the needed read/write rights!");
    }

    /**
     * Get all the payment methods, that are available for the merchant by Mpay24
     *
     * @return ListPaymentMethodsResponse
     */
    public function listPaymentMethods()
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.Mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ListPaymentMethods');
        $operation = $body->appendChild($operation);

        $xmlMerchantID = $xml->createElement('merchantID', $this->config->getMerchantId());
        $operation->appendChild($xmlMerchantID);

        $this->request = $xml->saveXML();

        $this->send();

        $result = new ListPaymentMethodsResponse($this->response);

        return $result;
    }

    /**
     * Start a secure payment through the Mpay24 payment window -
     * the sensible data (credit card numbers, bank account numbers etc)
     * is (will be) not saved in the shop
     *
     * @param string $mdxi The mdxi xml, which contains the shopping cart
     *
     * @return PaymentResponse
     */
    public function selectPayment($mdxi)
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.Mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:SelectPayment');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantId());
        $operation->appendChild($merchantID);

        $xmlMDXI = $xml->createElement('mdxi', htmlspecialchars($mdxi));
        $operation->appendChild($xmlMDXI);

        $this->request = $xml->saveXML();

        $this->send();

        $result = new PaymentResponse($this->response);

        return $result;
    }

    /**
     * Start a secure payment using the Mpay24 Tokenizer.
     *
     * @param string $pType The payment type used for the tokenization (currently supported 'CC')
     * @param array  $additional
     *
     * @return PaymentTokenResponse
     */
    public function createTokenPayment($pType, array $additional = [])
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.Mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:CreatePaymentToken');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantId());
        $operation->appendChild($merchantID);

        $pType = $xml->createElement('pType', $pType);
        $operation->appendChild($pType);

        foreach ($additional as $k => $v) {
            $buf = $xml->createElement($k, $v);
            $operation->appendChild($buf);
        }

        $this->request = $xml->saveXML();

        $this->send();

        $result = new PaymentTokenResponse($this->response);

        return $result;
    }

    /**
     * Initialize a manual callback to Mpay24 in order to check the information provided by PayPal
     *
     * @param        $type
     * @param string $tid The TID used for the transaction
     * @param array  $payment
     * @param array  $additional
     *
     * @return PaymentResponse
     */
    public function acceptPayment($type, $tid, $payment = [], $additional = [])
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElement('etp:AcceptPayment');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantId());
        $operation->appendChild($merchantID);

        $xmlTID = $xml->createElement('tid', $tid);
        $operation->appendChild($xmlTID);

        $xmlPType = $xml->createElement('pType', $type);
        $operation->appendChild($xmlPType);

        $xmlPayment = $xml->createElement('payment');
        $xmlPayment = $operation->appendChild($xmlPayment);
        $xmlPayment->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'etp:Payment' . $type);

        foreach ($payment as $k => $v) {
            $buf = $xml->createElement($k, $v);
            $xmlPayment->appendChild($buf);
        }

        $this->appendArray($operation, $additional, $xml);

        $this->request = $xml->saveXML();

        $this->send();
        $result = new PaymentResponse($this->response);

        return $result;
    }

    /**
     * Initialize a manual callback to Mpay24 in order to check the information provided by PayPal
     *
     * @param string $requestString The callback request to Mpay24
     * @param string $paymentType   The payment type which will be used for the express checkout (PAYPAL or MASTERPASS)
     *
     * @return PaymentResponse
     */
    public function manualCallback($requestString, $paymentType)
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElement('etp:ManualCallback');
        $operation = $body->appendChild($operation);

        $requestXML               = new DOMDocument("1.0", "UTF-8");
        $requestXML->formatOutput = true;
        $requestXML->loadXML($requestString);

        $requestNode = $requestXML->getElementsByTagName("AcceptPayment")->item(0);

        foreach ($requestNode->childNodes as $child) {
            $child = $xml->importNode($child, true);
            $operation->appendChild($child);

            if ($child->nodeName == 'paymentCallback') {
                $child->setAttributeNS(
                    'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:type',
                    "etp:Callback$paymentType"
                );
            }
        }

        $this->request = $xml->saveXML();

        $this->send();

        $result = new PaymentResponse($this->response);

        return $result;
    }

    /**
     * Clear a transaction with an amount
     *
     * @param int    $mPAYTid  The Mpay24 transaction ID
     * @param int    $amount   The amount to be cleared multiplay by 100
     * @param string $currency 3-digit ISO currency code: EUR, USD, etc
     *
     * @return ManagePaymentResponse
     */
    public function manualClear($mPAYTid, $amount, $currency)
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.Mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualClear');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantId());
        $operation->appendChild($merchantID);

        $clearingDetails = $xml->createElement('clearingDetails');
        $clearingDetails = $operation->appendChild($clearingDetails);

        $xmlMPayTid = $xml->createElement('mpayTID', $mPAYTid);
        $clearingDetails->appendChild($xmlMPayTid);

        $price = $xml->createElement('amount', $amount);
        $clearingDetails->appendChild($price);

        $this->request = $xml->saveXML();

        $this->send();

        $result = new ManagePaymentResponse($this->response);

        return $result;
    }

    /**
     * Credit a transaction with an amount
     *
     * @param int    $mPAYTid  The Mpay24 transaction ID
     * @param int    $amount   The amount to be credited multiplay by 100
     * @param string $currency 3-digit ISO currency code: EUR, USD, etc
     * @param string $customer The name of the customer, who has paid
     *
     * @return ManagePaymentResponse
     */
    public function ManualCredit($mPAYTid, $amount, $currency, $customer)
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualCredit');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantId());
        $operation->appendChild($merchantID);

        $xmlMPayTid = $xml->createElement('mpayTID', $mPAYTid);
        $operation->appendChild($xmlMPayTid);

        $price = $xml->createElement('amount', $amount);
        $operation->appendChild($price);

        $this->request = $xml->saveXML();

        $this->send();

        $result = new ManagePaymentResponse($this->response);

        return $result;
    }

    /**
     * Cancel a transaction
     *
     * @param int $mPAYTid The mPAY24 transaction ID for the transaction you want to cancel
     *
     * @return ManagePaymentResponse
     */
    public function manualReverse($mPAYTid)
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualReverse');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantId());
        $operation->appendChild($merchantID);

        $xmlMPayTid = $xml->createElement('mpayTID', $mPAYTid);
        $operation->appendChild($xmlMPayTid);

        $this->request = $xml->saveXML();

        $this->send();

        $result = new ManagePaymentResponse($this->response);

        return $result;
    }

    /**
     * Get all the information for a transaction, supported by mPAY24
     *
     * @param int    $mpay24tid The mPAY24 transaction ID
     * @param string $tid       The transaction ID from your shop
     *
     * @return TransactionStatusResponse
     */
    public function transactionStatus($mpay24tid = null, $tid = null)
    {
        $xml  = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:TransactionStatus');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantId());
        $operation->appendChild($merchantID);

        if ($mpay24tid) {
            $xmlMPayTid = $xml->createElement('mpayTID', $mpay24tid);
            $operation->appendChild($xmlMPayTid);
        } else {
            $xmlTid = $xml->createElement('tid', $tid);
            $operation->appendChild($xmlTid);
        }

        $this->request = $xml->saveXML();

        $this->send();

        $result = new TransactionStatusResponse($this->response);

        return $result;
    }

    /**
     * Encoded the parameters (AES256-CBC) for the pay link and return them
     *
     * @param array $params The parameters, which are going to be posted to mPAY24
     *
     * @return string
     */
    public function flexLink($params)
    {
        $paramsString = "";

        foreach ($params as $key => $value) {
            $paramsString .= "$key=$value&";
        }

        $encryptedParams = $this->ssl_encrypt($this->config->getFlexLinkPassword(), $paramsString);

        return $encryptedParams;
    }

    /**
     * Create a DOMDocument and prepare it for SOAP request: set Envelope, NameSpaces, create empty Body
     *
     * @return DOMDocument
     */
    private function buildEnvelope()
    {
        $soap_xml               = new DOMDocument("1.0", "UTF-8");
        $soap_xml->formatOutput = true;

        $envelope = $soap_xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Envelope');
        $envelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $envelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:etp',
            'https://www.mpay24.com/soap/etp/1.5/ETP.wsdl');
        $envelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $envelope = $soap_xml->appendChild($envelope);

        $body = $soap_xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Body');
        $envelope->appendChild($body);

        return $soap_xml;
    }

    /**
     * Create a curl request and send the cretaed SOAP XML
     */
    private function send()
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
            curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/bin/cacert.pem');

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

            if ($this->config->isEnableCurlLog()) {
                fclose($fh);
            }
        } catch (\Exception $e) {
            if ($this->config->isTestSystem()) {
                $dieMSG = "Your request couldn't be sent because of the following error:" . "\n" . curl_error(
                        $ch
                    ) . "\n" . $e->getMessage() . ' in ' . $e->getFile() . ', line: ' . $e->getLine() . '.';
            } else {
                $dieMSG = self::LIVE_ERROR_MSG;
            }

            echo $dieMSG;
        }
    }

    /**
     * Encode data (AES256-CBC) using a password
     *
     * @deprecated As mcrypt is deprecated in PHP7 and it will be removed in PHP7.2 is good idea to switch to OpenSSL
     *
     * @param string $pass The password, used for the encoding
     * @param string $data The data, that should be encoded
     *
     * @return string
     */
    private function ssl_encrypt($pass, $data)
    {
        // Set a random salt
        $salt = substr(md5(mt_rand(), true), 8);

        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad   = $block - (strlen($data) % $block);

        $data = $data . str_repeat(chr($pad), $pad);

        // Setup encryption parameters
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, "", MCRYPT_MODE_CBC, "");

        $key_len = mcrypt_enc_get_key_size($td);
        $iv_len  = mcrypt_enc_get_iv_size($td);

        $total_len = $key_len + $iv_len;
        $salted    = '';
        $dx        = '';

        // Salt the key and iv
        while (strlen($salted) < $total_len) {
            $dx = md5($dx . $pass . $salt, true);
            $salted .= $dx;
        }

        $key = substr($salted, 0, $key_len);
        $iv  = substr($salted, $key_len, $iv_len);

        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return chunk_split(array_shift(unpack('H*', 'Salted__' . $salt . $encrypted_data)), 32, "\r\n");
    }

    /**
     * @param DOMNode     $parent
     * @param array       $list
     * @param DOMDocument $document
     */
    protected function appendArray(DOMNode &$parent, array &$list, &$document = null)
    {
        if (is_null($document)) {
            $document = new DOMDocument();
        }

        foreach ($list as $name => $value) {
            if (is_array($value)) {
                $element = $document->createElement($name);
                $this->appendArray($element, $value, $document);
                $parent->appendChild($element);
            } else {
                $element = $document->createElement($name, $value);
                $parent->appendChild($element);
            }
        }
    }
}

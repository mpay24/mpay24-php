<?php

namespace mPay24;

use DOMDocument;

use mPay24\Responses\PaymentResponse;
use mPay24\Responses\PaymentTokenResponse;
use mPay24\Responses\ManagePaymentResponse;
use mPay24\Responses\TransactionStatusResponse;
use mPay24\Responses\ListPaymentMethodsResponse;

/**
 * Main mPAY24 PHP APIs Class.
 *
 * The MPAY24SDK class provides the communication functioanallity. It hold's all the sensitive data (merchant ID, SOAP password, etc) and
 * build the SOAP request, sent to mPAY24.
 *
 * @author              mPAY24 GmbH <support@mpay24.com>
 * @filesource          MPAY24SDK.php
 * @license MIT
 */
class MPAY24SDK
{
    /**
     * An error message, that will be displayed to the user in case you are using the LIVE system
     * @const LIVE_ERROR_MSG
     */
    const LIVE_ERROR_MSG = "We are sorry, an error occured - please contact the merchant!";

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
    const VERSION = "3.0.1"; // TODO: check if you want to change it, because of the reason updates

    /**
     * The fix (envelope) part of the soap xml, which is to be sent to mPAY24
     *
     * @var string
     */
    private $soap_xml = "";

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
     * @var MPay24Config
     */
    protected $config;

    public function __construct( MPay24Config &$config = null )
    {
        if ( is_null($config) ){
            $config = new MPay24Config();
        }

        $this->config = $config;
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
     * @deprecated Use Configuration Object instated
     */
    public function configureFlexLINK( $spid, $password, $test)
    {
        $this->config->setSPID($spid);
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
        return $this->config->getMerchantID();
    }

    /**
     * Get the SPID, which was set by the function configureFlexLINK($spid, $password, $test)
     *
     * @return string
     */
    public function getSPID()
    {
        return $this->config->getSPID();
    }

    /**
     * Get the system, which should be used for flexLINK (test -> 'test' or live -> 'www')
     *
     * @return string
     */
    public function getFlexLINKSystem()
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
    public function setDebug( $debug )
    {
        $this->config->setDebug( (bool) $debug);
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
     * Return MPay24 Log Path
     *
     * @return string
     */
    public function getMPya24LogPath()
    {
        return $this->config->getLogPath() . '/' . $this->config->getLogFile();
    }

    /**
     * Return MPay24 Curl Log Path
     *
     * @return string
     */
    public function getMPya24CurlLogPath()
    {
        return $this->config->getLogPath() . '/' . $this->config->getCurlLogFile();
    }

    /**
     * In case the test system is used, show die with the real error message, otherwise, show the defined constant error LIVE_ERROR_MSG
     *
     * @param string $msg The message, which is shown to the user
     * @throws \Exception
     */
    public function dieWithMsg( $msg )
    {
        if ( $this->config->isTestSystem() ) {
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
    public function printMsg( $msg )
    {
        if ( $this->config->isTestSystem() ) {
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
        $errors = error_get_last();
        $message = $errors['message'];
        $path = substr(
            $message,
            strpos($message, 'fopen(') + 6,
            strpos($message, ')') - (strpos($message, 'fopen(') + 6)
        );
        $this->dieWithMsg("Can't open file '$path'! Please set the needed read/write rights!");
    }

    /**
     * Get all the payment methods, that are available for the merchant by mPAY24
     *
     * @return ListPaymentMethodsResponse
     */
    public function ListPaymentMethods()
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ListPaymentMethods');
        $operation = $body->appendChild($operation);

        $xmlMerchantID = $xml->createElement('merchantID', $this->config->getMerchantID());
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
     * @param ORDER $mdxi The mdxi xml, which contains the shopping cart
     * @return PaymentResponse
     */
    public function SelectPayment( $mdxi )
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:SelectPayment');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantID());
        $merchantID = $operation->appendChild($merchantID);

        $xmlMDXI = $xml->createElement('mdxi', htmlspecialchars($mdxi));
        $xmlMDXI = $operation->appendChild($xmlMDXI);

        $this->request = $xml->saveXML();

        $this->send();

        $result = new PaymentResponse($this->response);

        return $result;
    }

    /**
     * Start a secure payment using the mPAY24 Tokenizer.
     *
     * @param string    $pType The payment type used for the tokenization (currently supported 'CC')
     * @param array     $additional
     * @return PaymentTokenResponse
     */
    public function CreateTokenPayment( $pType, array $additional = [] )
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:CreatePaymentToken');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantID());
        $merchantID = $operation->appendChild($merchantID);

        $pType = $xml->createElement('pType', $pType);
        $pType = $operation->appendChild($pType);

        foreach ( $additional as $k => $v ) {
            $buf = $xml->createElement($k, $v);
            $buf = $operation->appendChild($buf);
        }

        $this->request = $xml->saveXML();

        $this->send();

        $result = new PaymentTokenResponse($this->response);

        return $result;
    }

    /**
     * Initialize a manual callback to mPAY24 in order to check the information provided by PayPal
     *
     * @param $type
     * @param string $tid The TID used for the transaction
     * @param array $payment
     * @param array $additional
     * @return PaymentResponse
     */
    public function AcceptPayment( $type, $tid, $payment = [], $additional = [] )
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElement('etp:AcceptPayment');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantID());
        $merchantID = $operation->appendChild($merchantID);

        $xmlTID = $xml->createElement('tid', $tid);
        $xmlTID = $operation->appendChild($xmlTID);

        $xmlPType = $xml->createElement('pType', $type);
        $xmlPType = $operation->appendChild($xmlPType);

        $xmlPayment = $xml->createElement('payment');
        $xmlPayment = $operation->appendChild($xmlPayment);
        $xmlPayment->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'etp:Payment'.$type);

        foreach ( $payment as $k => $v ) {
            $buf = $xml->createElement($k, $v);
            $buf = $xmlPayment->appendChild($buf);
        }

        foreach ( $additional as $k => $v ) {
            $buf = $xml->createElement($k, $v);
            $buf = $operation->appendChild($buf);
        }

        $this->request = $xml->saveXML();

        $this->send();
        $result = new PaymentResponse($this->response);

        return $result;
    }

    /**
     * Initialize a manual callback to mPAY24 in order to check the information provided by PayPal
     *
     * @param string $requestString The callback request to mPAY24
     * @param string $paymentType The payment type which will be used for the express checkout (PAYPAL or MASTERPASS)
     * @return PaymentResponse
     */
    public function ManualCallback( $requestString, $paymentType )
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElement('etp:ManualCallback');
        $operation = $body->appendChild($operation);

        $requestXML = new DOMDocument("1.0", "UTF-8");
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
     * @param int $mPAYTid The mPAY24 transaction ID
     * @param int $amount The amount to be cleared multiplay by 100
     * @param string $currency 3-digit ISO currency code: EUR, USD, etc
     * @return ManagePaymentResponse
     */
    public function ManualClear( $mPAYTid, $amount, $currency )
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualClear');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantID());
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
     * @param int $mPAYTid The mPAY24 transaction ID
     * @param int $amount The amount to be credited multiplay by 100
     * @param string $currency 3-digit ISO currency code: EUR, USD, etc
     * @param string $customer The name of the customer, who has paid
     * @return ManagePaymentResponse
     */
    public function ManualCredit( $mPAYTid, $amount, $currency, $customer )
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualCredit');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantID());
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
     * @param int $mPAYTid The mPAY24 transaction ID for the transaction you want to cancel
     * @return ManagePaymentResponse
     */
    public function ManualReverse($mPAYTid)
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:ManualReverse');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantID());
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
     * @param int $mPAYTid The mPAY24 transaction ID
     * @param string $tid The transaction ID from your shop
     * @return TransactionStatusResponse
     */
    public function TransactionStatus( $mPAYTid = null, $tid = null )
    {
        $xml = $this->buildEnvelope();
        $body = $xml->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body')->item(0);

        $operation = $xml->createElementNS('https://www.mpay24.com/soap/etp/1.5/ETP.wsdl', 'etp:TransactionStatus');
        $operation = $body->appendChild($operation);

        $merchantID = $xml->createElement('merchantID', $this->config->getMerchantID());
        $merchantID = $operation->appendChild($merchantID);

        if ($mPAYTid) {
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
     * Encoded the parameters (AES256-CBC) for the pay link and return them
     *
     * @param array $params The parameters, which are going to be posted to mPAY24
     * @return string
     */
    public function flexLINK( $params )
    {
        $paramsString = "";

        foreach ( $params as $key => $value ) {
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
        $this->soap_xml = new DOMDocument("1.0", "UTF-8");
        $this->soap_xml->formatOutput = true;

        $envelope = $this->soap_xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Envelope');
        $envelope->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $envelope->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:etp',
            'https://www.mpay24.com/soap/etp/1.5/ETP.wsdl'
        );
        $envelope->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $envelope = $this->soap_xml->appendChild($envelope);

        $body = $this->soap_xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Body');
        $body = $envelope->appendChild($body);

        return $this->soap_xml;
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
        curl_setopt($ch, CURLOPT_USERPWD, 'u' . $this->config->getMerchantID() . ':' . $this->config->getSoapPassword());
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ( $this->config->isEnableCurlLog() ) {
            $fh = fopen($this->getMPya24CurlLogPath(), 'a+') or $this->permissionError();

            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_STDERR, $fh);
        }

        try {
            curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/bin/cacert.pem');

            if ($this->config->getProxyHost())
            {
                curl_setopt($ch, CURLOPT_PROXY, $this->config->getProxyHost() . ':' . $this->config->getProxyPort());

                if ($this->config->getProxyUser())
                {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->config->getProxyUser() . ':' . $this->config->getProxyPass());
                }

                if ($this->config->isVerifyPeer() !== true)
                {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->config->isVerifyPeer());
                }
            }

            $this->response = curl_exec($ch);
            curl_close($ch);

            if ($this->config->isEnableCurlLog()) {
                fclose($fh);
            }
        } catch ( \Exception $e ) {
            if ( $this->config->isTestSystem() )
            {
                $dieMSG = "Your request couldn't be sent because of the following error:"."\n".curl_error(
                        $ch
                    )."\n".$e->getMessage().' in '.$e->getFile().', line: '.$e->getLine().'.';
            }
            else
            {
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
     * @return string
     */
    private function ssl_encrypt( $pass, $data )
    {
        // Set a random salt
        $salt = substr(md5(mt_rand(), true), 8);

        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($data) % $block);

        $data = $data.str_repeat(chr($pad), $pad);

        // Setup encryption parameters
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, "", MCRYPT_MODE_CBC, "");

        $key_len = mcrypt_enc_get_key_size($td);
        $iv_len = mcrypt_enc_get_iv_size($td);

        $total_len = $key_len + $iv_len;
        $salted = '';
        $dx = '';

        // Salt the key and iv
        while ( strlen($salted) < $total_len ) {
            $dx = md5($dx.$pass.$salt, true);
            $salted .= $dx;
        }

        $key = substr($salted, 0, $key_len);
        $iv = substr($salted, $key_len, $iv_len);

        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return chunk_split(array_shift(unpack('H*', 'Salted__'.$salt.$encrypted_data)), 32, "\r\n");
    }
}

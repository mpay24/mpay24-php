<?php

namespace Mpay24;

use Exception;
use InvalidArgumentException;

include_once(dirname(__FILE__) . '/../config.php');

/**
 * Class Mpay24Config
 * @package    Mpay24
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource Mpay24Config.php
 * @license    MIT
 */
class Mpay24Config
{
    /**
     * @var int $merchantID
     *          5-digit account number, supported by Mpay24
     *
     *          TEST accounts - starting with 9
     *
     *          LIVE account - starting with 7
     */
    protected $merchantID;

    /**
     * @var string $soapPassword
     *          The webservice's password, supported by mPAY24
     */
    protected $soapPassword;

    /**
     * @var bool $testSystem
     *          true - when you want to use the TEST system
     *
     *          false - when you want to use the LIVE system
     */
    protected $testSystem;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var string $proxyHost The host name in case you are behind a proxy server ('' when not)
     */
    protected $proxyHost;

    /**
     * @var int $proxyPort 4-digit port number in case you are behind a proxy server ('' when not)
     */
    protected $proxyPort;

    /**
     * @var string $proxyUser The proxy user in case you are behind a proxy server ('' when not)
     */
    protected $proxyUser;

    /**
     * @var string $proxyPass The proxy password in case you are behind a proxy server ('' when not)
     */
    protected $proxyPass;

    /**
     * @var bool $verifyPeer Set as false to stop cURL from verifying the peer's certificate
     */
    protected $verifyPeer;

    /**
     * @var bool
     */
    protected $enableCurlLog;

    /**
     * SPID (supported from mPAY24).
     *
     * @var string
     */
    protected $spid;

    /**
     * The flexLink password (supproted from mPAY24)
     *
     * @var string
     */
    protected $flexLinkPassword;

    /**
     * @var bool $testSystem
     *          true - when you want to use the TEST system
     *
     *          false - when you want to use the LIVE system
     */
    protected $flexLinkTestSystem;

    /**
     * @var string
     */
    protected $curl_log_file;

    /**
     * @var string
     */
    protected $log_file;

    /**
     * @var string
     */
    protected $log_path;

	/**
	 * @var string
	 */
	protected $ca_cert_path;

	/**
	 * @var string
	 */
	protected $ca_cert_file_name;

    public function __construct()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_array($args[0])) {
            $args = $args[0];
        }

        // define if not defined, for backwards compatibility
	    defined('MPAY24_CA_CERT_PATH') or define('MPAY24_CA_CERT_PATH', dirname(__FILE__) . '/bin/');
	    defined('MPAY24_CA_CERT_FILE_NAME') or define('MPAY24_CA_CERT_FILE_NAME', 'cacert.pem');

        $merchantID         = (isset($args[0]) ? $args[0] : MPAY24_MERCHANT_ID);
        $soapPassword       = (isset($args[1]) ? $args[1] : MPAY24_SOAP_PASS);
        $testSystem         = (isset($args[2]) ? $args[2] : MPAY24_TEST_SYSTEM);
        $debug              = (isset($args[3]) ? $args[3] : MPAY24_DEBUG);
        $proxyHost          = (isset($args[4]) ? $args[4] : MPAY24_PROXY_HOST);
        $proxyPort          = (isset($args[5]) ? $args[5] : MPAY24_PROXY_PORT);
        $proxyUser          = (isset($args[6]) ? $args[6] : MPAY24_PROXY_USER);
        $proxyPass          = (isset($args[7]) ? $args[7] : MPAY24_PROXY_PASS);
        $verifyPeer         = (isset($args[8]) ? $args[8] : MPAY24_VERIFY_PEER);
        $enableCurlLog      = (isset($args[9]) ? $args[9] : MPAY24_ENABLE_CURL_LOG);
        $spid               = (isset($args[10]) ? $args[10] : MPAY24_SPID);
        $flexLinkPassword   = (isset($args[11]) ? $args[11] : MPAY24_FLEX_LINK_PASS);
        $flexLinkTestSystem = (isset($args[12]) ? $args[12] : MPAY24_FLEX_LINK_TEST_SYSTEM);
        $log_file           = (isset($args[13]) ? $args[13] : MPAY24_LOG_FILE);
        $log_path           = (isset($args[14]) ? $args[14] : MPAY24_LOG_PATH);
        $curl_log_file      = (isset($args[15]) ? $args[15] : MPAY24_CURL_LOG_FILE);
        $ca_cert_path       = (isset($args[16]) ? $args[16] : MPAY24_CA_CERT_PATH);
        $ca_cert_file_name  = (isset($args[17]) ? $args[17] : MPAY24_CA_CERT_FILE_NAME);

        $this->useTestSystem($testSystem);
        $this->setMerchantID($merchantID);
        $this->setSoapPassword($soapPassword);
        $this->setDebug($debug);
        $this->setProxyHost($proxyHost);
        $this->setProxyPort($proxyPort);
        $this->setProxyHost($proxyUser);
        $this->setProxyPass($proxyPass);
        $this->setVerifyPeer($verifyPeer);
        $this->setEnableCurlLog($enableCurlLog);
        $this->setSpid($spid);
        $this->setFlexLinkPassword($flexLinkPassword);
        $this->useFlexLinkTestSystem($flexLinkTestSystem);
        $this->setLogFile($log_file);
        $this->setLogPath($log_path);
        $this->setCurlLogFile($curl_log_file);
        $this->setCaCertPath($ca_cert_path);
        $this->setCaCertFileName($ca_cert_file_name);
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantID;
    }

    /**
     * @param string $merchantID
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function setMerchantID($merchantID)
    {
        if (preg_match('/^(7|9)\d{4}$/', $merchantID) !== 1) {
            if ($this->isTestSystem()) {
                throw new InvalidArgumentException("The merchant ID '$merchantID' you have given is wrong, it must be 5-digit number and starts with 7 or 9!");
            } else {
                throw new Exception();
            }
        }

        $this->merchantID = $merchantID;
    }

    /**
     * @return string
     */
    public function getSoapPassword()
    {
        return $this->soapPassword;
    }

    /**
     * @param string $soapPassword
     */
    public function setSoapPassword($soapPassword)
    {
        $this->soapPassword = $soapPassword;
    }

    /**
     * @return bool
     */
    public function isTestSystem()
    {
        return $this->testSystem;
    }

    /**
     * @param bool $testSystem
     */
    public function useTestSystem($testSystem)
    {
        $this->testSystem = (bool)$testSystem;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool)$debug;
    }

    /**
     * @return string
     */
    public function getProxyHost()
    {
        return $this->proxyHost != '' && $this->proxyPort != "" ? $this->proxyHost : '';
    }

    /**
     * @param string $proxyHost
     */
    public function setProxyHost($proxyHost)
    {
        $this->proxyHost = $proxyHost;
    }

    /**
     * @return int|string
     */
    public function getProxyPort()
    {
        return $this->proxyPort != '' && $this->proxyHost != "" ? $this->proxyPort : '';
    }

    /**
     * @param int $proxyPort
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function setProxyPort($proxyPort)
    {
        if ($proxyPort != null && preg_match('/^d{4}$/', $proxyPort) !== 1) {
            if ($this->isTestSystem()) {
                throw new InvalidArgumentException("The proxy port '$proxyPort' you have given must be numeric!");
            } else {
                throw new Exception();
            }
        }

        $this->proxyPort = $proxyPort;
    }

    /**
     * @return string
     */
    public function getProxyUser()
    {
        return $this->proxyUser != '' && $this->proxyPass != "" ? $this->proxyUser : '';
    }

    /**
     * @param string $proxyUser
     */
    public function setProxyUser($proxyUser)
    {
        $this->proxyUser = $proxyUser;
    }

    /**
     * @return string
     */
    public function getProxyPass()
    {
        return $this->proxyPass != '' && $this->proxyUser != "" ? $this->proxyPass : '';
    }

    /**
     * @param string $proxyPass
     */
    public function setProxyPass($proxyPass)
    {
        $this->proxyPass = $proxyPass;
    }

    /**
     * @return bool
     */
    public function isVerifyPeer()
    {
        return $this->verifyPeer;
    }

    /**
     * @param bool $verifyPeer
     */
    public function setVerifyPeer($verifyPeer)
    {
        $this->verifyPeer = (bool)$verifyPeer;
    }

    /**
     * @return bool
     */
    public function isEnableCurlLog()
    {
        return $this->enableCurlLog;
    }

    /**
     * @param bool $enableCurlLog
     */
    public function setEnableCurlLog($enableCurlLog)
    {
        $this->enableCurlLog = (bool)$enableCurlLog;
    }

    /**
     * @return string
     */
    public function getSPID()
    {
        return $this->spid;
    }

    /**
     * @param string $spid
     */
    public function setSpid($spid)
    {
        $this->spid = $spid;
    }

    /**
     * @return string
     */
    public function getFlexLinkPassword()
    {
        return $this->flexLinkPassword;
    }

    /**
     * @param string $flexLinkPassword
     */
    public function setFlexLinkPassword($flexLinkPassword)
    {
        $this->flexLinkPassword = $flexLinkPassword;
    }

    /**
     * @return bool
     */
    public function isFlexLinkTestSystem()
    {
        return $this->flexLinkTestSystem;
    }

    /**
     * @param bool $flexLinkTestSystem
     */
    public function useFlexLinkTestSystem($flexLinkTestSystem)
    {
        $this->flexLinkTestSystem = (bool)$flexLinkTestSystem;
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->log_file;
    }

    /**
     * @param string $log_file
     */
    public function setLogFile($log_file)
    {
        $this->log_file = ltrim($log_file, "\\");
    }

    /**
     * @return string
     */
    public function getLogPath()
    {
        return $this->log_path;
    }

    /**
     * @param string $log_path
     */
    public function setLogPath($log_path)
    {
        $this->log_path = rtrim($log_path, "\\");
    }

    /**
     * @return string
     */
    public function getCurlLogFile()
    {
        return $this->curl_log_file;
    }

    /**
     * @param string $curl_log_file
     */
    public function setCurlLogFile($curl_log_file)
    {
        $this->curl_log_file = ltrim($curl_log_file, "\\");
    }

	/**
	 * @return string
	 */
	public function getCaCertPath()
	{
		return $this->ca_cert_path;
	}

	/**
	 * @param string $ca_cert_path
	 */
	public function setCaCertPath($ca_cert_path)
	{
		$this->ca_cert_path = $ca_cert_path;
	}

	/**
	 * @return string
	 */
	public function getCaCertFileName()
	{
		return $this->ca_cert_file_name;
	}

	/**
	 * @param string $ca_cert_file_name
	 */
	public function setCaCertFileName($ca_cert_file_name)
	{
		$this->ca_cert_file_name = $ca_cert_file_name;
	}
}

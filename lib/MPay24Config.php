<?php

namespace mPay24;

use Exception;
use InvalidArgumentException;

include_once(dirname(__FILE__) . '/../config.php');

class MPay24Config
{
    /**
     * @var int $merchantID
     *          5-digit account number, supported by mPAY24
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
     *          TRUE - when you want to use the TEST system
     *
     *          FALSE - when you want to use the LIVE system
     */
    protected $testSystem;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var string $proxyHost The host name in case you are behind a proxy server ("" when not)
     */
    protected $proxyHost;

    /**
     * @var int $proxyPort 4-digit port number in case you are behind a proxy server ("" when not)
     */
    protected $proxyPort;

    /**
     * @var string $proxyUser The proxy user in case you are behind a proxy server ("" when not)
     */
    protected $proxyUser;

    /**
     * @var string $proxyPass The proxy password in case you are behind a proxy server ("" when not)
     */
    protected $proxyPass;

    /**
     * @var bool $verifyPeer Set as FALSE to stop cURL from verifying the peer's certificate
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
     * The flexLINK password (supproted from mPAY24)
     *
     * @var string
     */
    protected $flexLinkPassword;

    /**
     * @var bool $testSystem
     *          TRUE - when you want to use the TEST system
     *
     *          FALSE - when you want to use the LIVE system
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

    public function __construct(
        $merchantID         = MPAY24_MERCHANT_ID,
        $soapPassword       = MPAY24_SOAP_PASS,
        $testSystem         = MPAY24_TEST_SYSTEM,
        $debug              = MPAY24_DEBUG,
        $proxyHost          = MPAY24_PROXY_HOST,
        $proxyPort          = MPAY24_PROXY_PORT,
        $proxyUser          = MPAY24_PROXY_USER,
        $proxyPass          = MPAY24_PROXY_PASS,
        $verifyPeer         = MPAY24_VERIFY_PEER,
        $enableCurlLog      = MPAY24_ENABLE_CURL_LOG,
        $sPid               = MPAY24_SPID,
        $flexLinkPassword   = MPAY24_FLEX_LINK_PASS,
        $flexLinkTestSystem = MPAY24_FLEX_LINK_TEST_SYSTEM,
        $log_file           = MPAY24_LOG_FILE,
        $log_path           = MPAY24_LOG_PATH,
        $curl_log_file      = MPAY24_CURL_LOG_FILE
    )
    {
        $this->setMerchantID($merchantID);
        $this->setSoapPassword($soapPassword);
        $this->useTestSystem($testSystem);
        $this->setDebug($debug);
        $this->setProxyHost($proxyHost);
        $this->setProxyPort($proxyPort);
        $this->setProxyHost($proxyUser);
        $this->setProxyPass($proxyPass);
        $this->setVerifyPeer($verifyPeer);
        $this->setEnableCurlLog($enableCurlLog);
        $this->setSPid($sPid);
        $this->setFlexLinkPassword($flexLinkPassword);
        $this->useFlexLinkTestSystem($flexLinkTestSystem);
        $this->setLogFile($log_file);
        $this->setLogPath($log_path);
        $this->setCurlLogFile($curl_log_file);
    }

    /**
     * @return string
     */
    public function getMerchantID()
    {
        return $this->merchantID;
    }

    /**
     * @param string $merchantID
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
        return (bool)$this->testSystem;
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
        return (bool)$this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool)$debug;
    }

    /**
     * @return null
     */
    public function getProxyHost()
    {
        return $this->proxyHost != '' && $this->proxyPort != "" ? $this->proxyHost : '';
    }

    /**
     * @param null $proxyHost
     */
    public function setProxyHost($proxyHost)
    {
        $this->proxyHost = $proxyHost;
    }

    /**
     * @return null
     */
    public function getProxyPort()
    {
        return $this->proxyPort != '' && $this->proxyHost != "" ? $this->proxyPort : '';
    }

    /**
     * @param null $proxyPort
     * @throws Exception
     */
    public function setProxyPort($proxyPort)
    {
        if ($proxyPort != null && (!is_numeric($proxyPort) || strlen($proxyPort) != 4)) {
            if ($this->isTestSystem()) {
                throw new InvalidArgumentException("The proxy port '$proxyPort' you have given must be numeric!");
            } else {
                throw new Exception();
            }
        }

        $this->proxyPort = $proxyPort;
    }

    /**
     * @return null
     */
    public function getProxyUser()
    {
        return $this->proxyUser != '' && $this->proxyPass != "" ? $this->proxyUser : '';
    }

    /**
     * @param null $proxyUser
     */
    public function setProxyUser($proxyUser)
    {
        $this->proxyUser = $proxyUser;
    }

    /**
     * @return null
     */
    public function getProxyPass()
    {
        return $this->proxyPass != '' && $this->proxyUser != "" ? $this->proxyPass : '';
    }

    /**
     * @param null $proxyPass
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
    public function getSpid()
    {
        return $this->spid;
    }

    /**
     * @param string $spid
     */
    public function setSPid($spid)
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
        return (bool)$this->flexLinkTestSystem;
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
        $this->log_file = $log_file;
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
        $this->log_path = $log_path;
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
        $this->curl_log_file = $curl_log_file;
    }
}

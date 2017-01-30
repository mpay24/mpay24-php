<?php

namespace mPay24;

use Exception;
use InvalidArgumentException;

/**
 * Class MPay24Config
 *
 * @author Stefan Polzer <develop@posit.at>
 * @filesource MPay24Config.php
 * @license MIT
 */
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
    protected $sPid;

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

    /**
     * MPay24Config constructor.
     * @param string    $merchantID
     * @param string    $soapPassword
     * @param bool      $testSystem
     * @param bool      $debug
     * @param null      $proxyHost
     * @param null      $proxyPort
     * @param null      $proxyUser
     * @param null      $proxyPass
     * @param bool      $verifyPeer
     * @param bool      $enableCurlLog
     * @param string    $sPid
     * @param string    $flexLinkPassword
     * @param bool      $flexLinkTestSystem
     * @param string    $logFile
     * @param string    $curlLogFile
     */
    function __construct( $merchantID = '9****', $soapPassword = '**********', $testSystem = true, $debug = true, $proxyHost = null, $proxyPort = null, $proxyUser = null, $proxyPass = null, $verifyPeer = true, $enableCurlLog = false, $sPid = 'abcdefghjklmnop', $flexLinkPassword = '**********', $flexLinkTestSystem = true, $logFile = 'mpay24.log', $curlLogFile = 'curl.log' )
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
        $this->setSPID($sPid);
        $this->setFlexLinkPassword($flexLinkPassword);
        $this->useFlexLinkTestSystem($flexLinkTestSystem);
        $this->setLogFile($logFile);
        $this->setCurlLogFile($curlLogFile);

        $logPath = dirname(__FILE__) . '/logs';
        $this->setLogPath($logPath);
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
     * @param $soapPassword
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
        $this->testSystem = (bool) $testSystem;
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
        $this->debug = (bool) $debug;
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
        $this->verifyPeer = (bool) $verifyPeer;
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
        $this->enableCurlLog = (bool) $enableCurlLog;
    }

    /**
     * @return string
     */
    public function getSPID()
    {
        return $this->sPid;
    }

    /**
     * @param string $sPid
     */
    public function setSPID($sPid)
    {
        $this->sPid = $sPid;
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
        $this->flexLinkTestSystem = (bool) $flexLinkTestSystem;
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
}

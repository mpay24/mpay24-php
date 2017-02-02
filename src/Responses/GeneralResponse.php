<?php

namespace Mpay24\Responses;

use DOMDocument;
use ErrorException;

/**
 * The GeneralResponse class contains the status of a response and return code, which was delivered by mPAY24 as an answer of your request
 *
 * Class GeneralResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource GeneralResponse.php
 * @license    MIT
 */
class GeneralResponse
{
    /**
     * The response as Dom Document Object
     *
     * @var DOMDocument
     */
    protected $responseAsDom;

    /**
     * The status of the request, which was sent to mPAY24
     *
     * @var string
     */
    protected $status;

    /**
     * The return code from the request, which was sent to mPAY24
     *
     * @var string
     */
    protected $returnCode;

    /**
     * Sets the basic values from the response from mPAY24: status and return code
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        if ('' != $response) {
            $this->responseAsDom = new DOMDocument();

            try {
                $this->responseAsDom->loadXML($response);
            } catch (ErrorException $e) {
                $this->status = 'ERROR';
                $this->returnCode = 'Unknown Error';

                if (preg_match('<title>401 Unauthorized</title>',$response)) {
                    $this->returnCode = "401 Unauthorized: check your merchant ID and password";
                }

                return;
            }

            if (!empty($this->responseAsDom) && is_object($this->responseAsDom)) {
                if ($this->responseAsDom->getElementsByTagName('status')->length == 0
                    || $this->responseAsDom->getElementsByTagName('returnCode')->length == 0
                ) {
                    $this->status     = "ERROR";
                    $this->returnCode = urldecode($response);
                } else {
                    $this->status     = $this->responseAsDom->getElementsByTagName('status')->item(0)->nodeValue;
                    $this->returnCode = $this->responseAsDom->getElementsByTagName('returnCode')->item(0)->nodeValue;
                }
            }
        } else {
            $this->status     = "ERROR";
            $this->returnCode = "The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!";
        }
    }

    /**
     * Get the status of the request, which was sent to mPAY24
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the return code from the request, which was sent to mPAY24
     *
     * @return string
     */
    public function getReturnCode()
    {
        return $this->returnCode;
    }

    /**
     * @return bool
     */
    public function hasNoError()
    {
        return $this->getStatus() != 'ERROR';
    }

    /**
     * @return bool
     */
    public function hasStatusOk()
    {
        return $this->getStatus() == 'OK';
    }

    /**
     * Set the status in the response, which was delivered by mPAY24
     *
     * @param string $status
     *          Status
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Set the return code in the response, which was delivered by mPAY24
     *
     * @param $returnCode
     *
     * @return mixed
     */
    protected function setReturnCode($returnCode)
    {
        return $this->returnCode = $returnCode;
    }
}

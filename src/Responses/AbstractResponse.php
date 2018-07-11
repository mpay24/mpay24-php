<?php

namespace Mpay24\Responses;

use DOMDocument;
use Exception;

/**
 * The GeneralResponse class contains the status of a response and return code, which was delivered by mPAY24 as an answer of your request
 *
 * Class AbstractResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource AbstractResponse.php
 * @license    MIT
 */
abstract class AbstractResponse
{
    const NAME_SPACE = 'https://www.mpay24.com/soap/etp/1.5/ETP.wsdl';

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
     * @var int
     */
    protected $createdAt;

    /**
     * Sets the basic values from the response from mPAY24: status and return code
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        $this->responseAsDom = new DOMDocument();
        if ('' != $response) {

            if (preg_match('/<title>401 Unauthorized<\/title>/', $response) == 1) {
                $this->status     = 'ERROR';
                $this->returnCode = "401 Unauthorized: check your merchant ID and password";

                return;
            }

            try {
                $this->responseAsDom->loadXML($response);
            } catch (Exception $e) {
                $this->status     = 'ERROR';
                $this->returnCode = 'Unknown Error';

                return;
            }

            if (!empty($this->responseAsDom) && is_object($this->responseAsDom)) {
                if ($this->responseAsDom->getElementsByTagName('status')->length == 0
                    || $this->responseAsDom->getElementsByTagName('returnCode')->length == 0
                ) {
                    $this->status     = "ERROR";
                    $this->returnCode = urldecode($response);

                    if ($this->responseAsDom->getElementsByTagName('faultcode')->length > 0
                        && $this->responseAsDom->getElementsByTagName('faultstring')->length > 0
                    ) {
                        $this->returnCode = $this->responseAsDom->getElementsByTagName('faultcode')->item(0)->nodeValue;
                        $this->returnCode .= ' - ';
                        $this->returnCode .= $this->responseAsDom->getElementsByTagName('faultstring')->item(0)->nodeValue;
                    }
                } else {
                    $this->status     = $this->responseAsDom->getElementsByTagName('status')->item(0)->nodeValue;
                    $this->returnCode = $this->responseAsDom->getElementsByTagName('returnCode')->item(0)->nodeValue;
                }
            }
        } else {
            $this->status     = "ERROR";
            $this->returnCode = "The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!";
        }

        $this->createdAt = time();
    }

    /**
     * Dumps the internal XML tree back into a string
     *
     * @return string
     */
    public function getXml()
    {
        return $this->responseAsDom->saveXML();
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

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $element
     *
     * @return \DOMElement
     */
    protected function getBody($element)
    {
        return $this->responseAsDom->getElementsByTagNameNS(self::NAME_SPACE, $element)->item(0);
    }
}

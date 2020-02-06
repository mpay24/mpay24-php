<?php

namespace Mpay24\Responses;

use DOMDocument;
use Exception;
use Mpay24\Exception\CouldNotLoadResponseXMLException;
use Mpay24\Exception\EmptyResponseException;
use Mpay24\Exception\MissingResponseReturnCodeException;
use Mpay24\Exception\MissingResponseStatusException;
use Mpay24\Exception\UnauthorizedAccessException;

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
     * @var Exception
     */
    protected $exception;

    /**
     * Sets the basic values from the response from mPAY24: status and return code
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        $this->responseAsDom = new DOMDocument();
        $this->createdAt     = time();

        try {
            if ('' == $response) {
                throw new EmptyResponseException();
            }

            if (preg_match('/<title>401 Unauthorized<\/title>/', $response) == 1) {
                throw new UnauthorizedAccessException();
            }

            try {
                $this->responseAsDom->loadXML($response);
            } catch (Exception $exception) {
                throw new CouldNotLoadResponseXMLException($exception->getMessage());
            }

            if (empty($this->responseAsDom)) {
                throw new EmptyResponseException();
            }

            if ($this->responseAsDom->getElementsByTagName('status')->length == 0) {
                throw new MissingResponseStatusException($this->getFaultMessage());
            }

            if ($this->responseAsDom->getElementsByTagName('returnCode')->length == 0) {
                throw new MissingResponseReturnCodeException($this->getFaultMessage());
            }
        } catch (Exception $exception) {
            $this->setStatus('ERROR');
            $this->setReturnCode($exception->getMessage());
            $this->exception = $exception;

            return;
        }

        $this->setStatus($this->responseAsDom->getElementsByTagName('status')->item(0)->nodeValue);
        $this->setReturnCode($this->responseAsDom->getElementsByTagName('returnCode')->item(0)->nodeValue);
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
    public function hasNoException()
    {
        return is_null($this->exception);
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
     * @param string $returnCode
     */
    protected function setReturnCode($returnCode)
    {
        $this->returnCode = $returnCode;
    }

    /**
     * @return string|null
     */
    protected function getFaultMessage()
    {
        if ($this->responseAsDom->getElementsByTagName('faultcode')->length > 0
            && $this->responseAsDom->getElementsByTagName('faultstring')->length > 0
        ) {
            $message = $this->responseAsDom->getElementsByTagName('faultcode')->item(0)->nodeValue;
            $message .= ' - ';
            $message .= $this->responseAsDom->getElementsByTagName('faultstring')->item(0)->nodeValue;

            return $message;
        }

        return null;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $element
     *
     * @return \DOMElement
     */
    protected function getBody($element)
    {
        return $this->responseAsDom->getElementsByTagNameNS(self::NAME_SPACE, $element)->item(0);
    }
}

<?php

namespace Mpay24\Responses;

/**
 * The TransactionStatusResponse class contains a generalResponse object and all the parameters, returned with the confirmation from mPAY24
 *
 * Class TransactionStatusResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource TransactionStatusResponse.php
 * @license    MIT
 */
class TransactionStatusResponse extends AbstractResponse
{
    /**
     * @var int
     */
    protected $paramCount = 0;

    /**
     * @var array
     */
    protected $transaction = [];

    /**
     * Sets the values for a transaction from the response from mPAY24: STATUS, PRICE, CURRENCY, LANGUAGE, etc
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            $this->parseResponse($this->getBody('TransactionStatusResponse'));
        }
    }

    /**
     * Get the count of all the paramerters for a transaction
     *
     * @return int
     */
    public function getParamCount()
    {
        return $this->paramCount;
    }

    /**
     * Get the parameters for a transaction, returned from mPAY24
     *
     * @return array
     */
    public function getParams()
    {
        return $this->transaction;
    }

    /**
     * Get the parameter's value, returned from mPAY24
     *
     * @param string $name
     *          The name of a parameter (for example: STATUS, PRICE, CURRENCY, etc)
     *
     * @return string|bool
     */
    public function getParam($name)
    {
        if (isset($this->transaction[$name])) {
            return $this->transaction[$name];
        } else {
            return false;
        }
    }

    /**
     * Parse the TransactionStatusResponse message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        $this->paramCount = $body->getElementsByTagName('parameter')->length;

        if ($this->paramCount > 0) {
            for ($i = 0; $i < $this->paramCount; $i++) {

                $parameter = $body->getElementsByTagName('parameter')->item($i);

                $name  = $parameter->getElementsByTagName('name')->item(0)->nodeValue;
                $value = $parameter->getElementsByTagName('value')->item(0)->nodeValue;

                $this->transaction[$name] = $value;
            }
        }
    }
}

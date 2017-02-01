<?php

namespace Mpay24\Responses;

use DOMDocument;

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
class TransactionStatusResponse extends GeneralResponse
{
    /**
     * Sets the values for a transaction from the response from mPAY24: STATUS, PRICE, CURRENCY, LANGUAGE, etc
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->responseAsDom->getElementsByTagName('name')->length != 0) {
            $paramCount = $this->responseAsDom->getElementsByTagName('name')->length;
            // TODO: check where this is coming from => transaction not found
            //$this->transaction['status'] = $this->getStatus();

            for ($i = 0; $i < $paramCount; $i++) {
                $this->transaction[strtolower($this->responseAsDom->getElementsByTagName('name')->item($i)->nodeValue)] = $this->responseAsDom->getElementsByTagName('value')->item($i)->nodeValue;
            }

            unset($this->params);
            unset($this->status);
            unset($this->returnCode);
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
     * @param string $i
     *          The name of a parameter (for example: STATUS, PRICE, CURRENCY, etc)
     *
     * @return array|bool
     */
    public function getParam($i)
    {
        if (isset($this->transaction[$i])) {
            return $this->transaction[$i];
        } else {
            return false;
        }
    }

    /**
     * Set a value for a parameter
     *
     * @param string $name
     *          The name of a parameter (for example: STATUS, PRICE, CURRENCY, etc)
     * @param string $value
     *          The value of the parameter
     */
    public function setParam($name, $value)
    {
        $this->transaction[$name] = $value;
    }
}

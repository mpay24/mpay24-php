<?php
namespace mPay24\Responses;

use DOMDocument;

/**
 * The TransactionStatusResponse class contains a generalResponse object and all the parameters, returned with the confirmation from mPAY24
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @filesource MPAY24SDK.php
 * @license MIT
 */
class TransactionStatusResponse extends GeneralResponse
{
    /**
     * An object, that represents the basic values from the response from mPAY24: status and return code
     *
     * @var string
     */
    var $generalResponse;

    /**
     * Sets the values for a transaction from the response from mPAY24: STATUS, PRICE, CURRENCY, LANGUAGE, etc
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    function __construct( $response ) {
        $this->generalResponse = new GeneralResponse($response);

        if( '' != $response ) {
            $responseAsDOM = new DOMDocument();
            $responseAsDOM->loadXML($response);

            if( $responseAsDOM && $responseAsDOM->getElementsByTagName('name')->length != 0 ) {
                $paramCount = $responseAsDOM->getElementsByTagName('name')->length;
                $this->transaction['status'] = $this->generalResponse->getStatus();

                for( $i = 0; $i < $paramCount; $i ++ ) {
                    $this->transaction[strtolower($responseAsDOM->getElementsByTagName('name')->item($i)->nodeValue)] = $responseAsDOM->getElementsByTagName('value')->item($i)->nodeValue;
                }
                unset($this->params);
                unset($this->status);
                unset($this->returnCode);
            }
        } else {
            $this->generalResponse->setStatus("ERROR");
            $this->generalResponse->setReturnCode("The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information!");
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
     * @return array|bool
     */
    public function getParam( $i )
    {
        if(isset($this->transaction[$i])) {
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
    public function setParam( $name, $value )
    {
        $this->transaction[$name] = $value;
    }

    /**
     * Get the object, that contains the basic values from the response from mPAY24: status and return code
     *
     * @return string
     */
    public function getGeneralResponse()
    {
        return $this->generalResponse;
    }
}

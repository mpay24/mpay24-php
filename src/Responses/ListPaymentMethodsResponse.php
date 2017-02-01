<?php

namespace Mpay24\Responses;

/**
 * The ListPaymentMethodsResponse class contains a generalResponse object and all the needed informarion for the active payment mothods (payment methods count, payment types, brands and descriptions)
 *
 * Class ListPaymentMethodsResponse
 * @package    Mpay24\Responses
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource ListPaymentMethodsResponse.php
 * @license    MIT
 */
class ListPaymentMethodsResponse extends GeneralResponse
{
    /**
     * The count of the payment methods, which are activated by mPAY24
     *
     * @var int
     */
    protected $all = 0;

    /**
     * A list with the payment types, activated by mPAY24
     *
     * @var array
     */
    protected $pTypes = [];

    /**
     * A list with the brands, activated by mPAY24
     *
     * @var array
     */
    protected $brands = [];

    /**
     * A list with the descriptions of the payment methods, activated by mPAY24
     *
     * @var array
     */
    protected $descriptions = [];

    /**
     * A list with the IDs of the payment methods, activated by mPAY24
     *
     * @var array
     */
    protected $pMethIds = [];

    /**
     * Sets the values for a payment from the response from mPAY24: count, payment types, brands and descriptions
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            if ($this->responseAsDom->getElementsByTagName('all')->length != 0) {

                $this->all = $this->responseAsDom->getElementsByTagName('all')->item(0)->nodeValue;

                for ($i = 0; $i < $this->all; $i++) {
                    $this->pTypes[$i]       = $this->responseAsDom->getElementsByTagName('pType')->item($i)->nodeValue;
                    $this->brands[$i]       = $this->responseAsDom->getElementsByTagName('brand')->item($i)->nodeValue;
                    $this->descriptions[$i] = $this->responseAsDom->getElementsByTagName('description')->item($i)->nodeValue;
                    $this->pMethIds[$i]     = $this->responseAsDom->getElementsByTagName('paymentMethod')->item($i)->getAttribute("id");
                }
            }
        }
    }

    /**
     * Get the count of the payment methods, returned from mPAY24
     *
     * @return int
     */
    public function getAll()
    {
        return $this->all;
    }

    /**
     * Get the payment types, returned from mPAY24
     *
     * @return array
     */
    public function getPTypes()
    {
        return $this->pTypes;
    }

    /**
     * Get the brands, returned from mPAY24
     *
     * @return array
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * Get the descriptions, returned from mPAY24
     *
     * @return array
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * Get the payment method IDs, returned from mPAY24
     *
     * @return array
     */
    public function getPMethIDs()
    {
        return $this->pMethIds;
    }

    /**
     * Get payment type, returned from mPAY24
     *
     * @param int $i
     *          The index of a payment type
     *
     * @return string
     */
    public function getPType($i)
    {
        return $this->pTypes[$i];
    }

    /**
     * Get brand, returned from mPAY24
     *
     * @param int $i
     *          The index of a brand
     *
     * @return string
     */
    public function getBrand($i)
    {
        return $this->brands[$i];
    }

    /**
     * Get description, returned from mPAY24
     *
     * @param int $i
     *          The index of a description
     *
     * @return string
     */
    public function getDescription($i)
    {
        return $this->descriptions[$i];
    }

    /**
     * Get payment method ID, returned from mPAY24
     *
     * @param int $i
     *          The index of an payment method ID
     *
     * @return int
     */
    public function getPMethID($i)
    {
        return $this->pMethIds[$i];
    }
}

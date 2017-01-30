<?php

namespace mPay24;

/**
 * The abstract MPay24flexLINK class provides abstract functions, which are used from the other functions in order to create a flexLINK
 *
 * @author mPAY24 GmbH <support@mpay24.com>
 * @filesource MPAY24.php
 * @license MIT
 */
class MPay24flexLINK
{
    /**
     * The mPAY24API Object, you are going to work with
     *
     * @var $mPAY24SDK
     */
    var $mPAY24SDK = null;

    /**
     * The constructor, which sets all the initial values to be able making flexLINK transactions.
     * In order to be able use this functionality, you should contact mPAY24 first.
     */
    function __construct()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_a($args[0], MPay24Config::class ))
        {
            $this->mPAY24SDK = new MPAY24SDK($args[0]);
        }
        else
        {
            $config = new MPay24Config();

            if (isset($args[0])){
                $config->setSPID($args[0]);
            }

            if (isset($args[1])){
                $config->setFlexLinkPassword($args[1]);
            }

            if (isset($args[2])){
                $config->useFlexLinkTestSystem($args[2]);
            }

            if (isset($args[3])){
                $config->setDebug($args[3]);
            }

            $this->mPAY24SDK = new MPAY24SDK($config);
        }

        $this->mPAY24SDK->checkRequirements(false, false, true);
    }

    /**
     * Encrypt the parameters you want to post to mPAY24 - see details
     *
     * @param string $invoice_id The invoice ID of the transaction
     * @param string $amount The amount which should be invoiced in 12.34
     * @param string $currency length = 3 (ISO currency code, e. g. "EUR")
     * @param string $language length = 2 (ISO currency code, e. g. "DE")
     * @param string $user_field A placeholder for free chosen user information
     * @param string $mode BillingAddress Mode (ReadWrite or ReadOnly)
     * @param string $salutation Salutation of the customer
     * @param string $name Name of the customer
     * @param string $street Billing address street
     * @param string $street2 Billing address street2
     * @param string $zip Billing address zip
     * @param string $city Billing address city
     * @param string $country Billing address country, length = 2 (ISO country code, e. g. "AT")
     * @param string $email Billing address e-mail
     * @param string $phone Billing address phone
     * @param string $success Success-URL
     * @param string $error Error-URL
     * @param string $confirmation Confirmation-URL
     * @param string $invoice_idVar Default = TID
     * @param string $amountVar Default = AMOUNT
     * @param string $currencyVar Default = CURRENCY
     * @param string $languageVar Default = LANGUAGE
     * @param string $user_fieldVar Default = USER_FIELD
     * @param string $modeVar Default = MODE
     * @param string $salutationVar Default = SALUTATION
     * @param string $nameVar Default = NAME
     * @param string $streetVar Default = STREET
     * @param string $street2Var Default = STREET2
     * @param string $zipVar Default = ZIP
     * @param string $cityVar Default = CITY
     * @param string $countryVar Default = COUNTRY
     * @param string $emailVar Default = EMAIL
     * @param string $phoneVar Default = PHONE
     * @param string $successVar Default = SUCCCESS_URL
     * @param string $errorVar Default = ERROR_URL
     * @param string $confirmationVar Default = CONFIRMATION_URL
     * @return string
     */
    function getEncryptedParams( // parameter values
        $invoice_id,
        $amount,
        $currency = null,
        $language = null,
        $user_field = null,
        $mode = null,
        $salutation = null,
        $name = null,
        $street = null,
        $street2 = null,
        $zip = null,
        $city = null,
        $country = null,
        $email = null,
        $phone = null,
        $success = null,
        $error = null,
        $confirmation = null,
        // parameters names
        $invoice_idVar = "TID",
        $amountVar = "AMOUNT",
        $currencyVar = "CURRENCY",
        $languageVar = "LANGUAGE",
        $user_fieldVar = "USER_FIELD",
        $modeVar = "MODE",
        $salutationVar = "SALUTATION",
        $nameVar = "NAME",
        $streetVar = "STREET",
        $street2Var = "STREET2",
        $zipVar = "ZIP",
        $cityVar = "CITY",
        $countryVar = "COUNTRY",
        $emailVar = "EMAIL",
        $phoneVar = "PHONE",
        $successVar = "SUCCESS_URL",
        $errorVar = "ERROR_URL",
        $confirmationVar = "CONFIRMATION_URL"
    ) {

        if ( !$this->mPAY24SDK ) {
            die("You are not allowed to define a constructor in the child class of MPay24flexLINK!");
        }

        $params[$invoice_idVar] = $invoice_id;
        $params[$amountVar] = $amount;

        if ( $currency == null ) {
            $currency = "EUR";
        }

        $params[$currencyVar] = $currency;

        if ( $language == null ) {
            $language = "DE";
        }

        $params[$languageVar] = $language;
        $params[$user_fieldVar] = $user_field;

        if ( $description == null ) {               //TODO: undefined variable $description => check where this is coming from
            $description = "Rechnungsnummer:";
        }

        $params[$descriptionVar] = $description;    //TODO: undefined variable $descriptionVar => check where this is coming from

        if ($mode == null) {
            $mode = "ReadWrite";
        }

        $params[$modeVar] = $mode;

        $params[$nameVar] = $name;
        $params[$streetVar] = $street;
        $params[$street2Var] = $street2;
        $params[$zipVar] = $zip;
        $params[$cityVar] = $city;

        if ( $country == null ) {
            $country = "AT";
        }

        $params[$countryVar] = $country;

        $params[$emailVar] = $email;
        $params[$successVar] = $success;
        $params[$errorVar] = $error;
        $params[$confirmationVar] = $confirmation;

        $parameters = $this->mPAY24SDK->flexLINK($params);

        return $parameters;
    }

    /**
     * Get the whole URL (flexLINK) to the mPAY24 pay page, used to pay an invoice
     *
     * @param string $encryptedParams The encrypted parameters, returned by the function getEncryptedParams
     * @return string An URL to pay
     */
    public function getPayLink( $encryptedParams )
    {
        return "https://" . $this->mPAY24SDK->getFlexLINKSystem() . ".mpay24.com/app/bin/checkout/".$this->mPAY24SDK->getSPID()."/$encryptedParams";
    }
}

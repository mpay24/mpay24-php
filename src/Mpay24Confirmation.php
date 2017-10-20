<?php

namespace Mpay24;

use InvalidArgumentException;

/**
 * The confirmation class allows you to make a basic validation of the GET parameters send by Mpay24
 *
 * Class Mpay24Confirmation
 * @package    Mpay24
 *
 * @property string OPERATION      // CONFIRMATION
 * @property string TID            // length <= 32
 * @property string STATUS         // RESERVED, BILLED, REVERSED, CREDITED, ERROR
 * @property int    PRICE          // length = 11 (e. g. "10" = "0,10")
 * @property string CURRENCY       // length = 3 (ISO currency code, e. g. "EUR")
 * @property string P_TYPE         // CC, ELV, EPS, GIROPAY, MAESTRO, PB, PSC, QUICK, PAYPAL, MPASS, BILLPAY, KLARNA, SOFORT, MASTERPASS
 * @property string BRAND          // MAESTRO, MASTERPASS, MPASS, PB, PAYPAL, PSC, EPS, GIROPAY, SOFORT, QUICK, AMEX, DINERS, JCB, MASTERCARD, VISA, ATOS, B4P, HOBEX-AT, HOBEX-DE, HOBEX-NL, BILLPAY, INVOICE, HP
 * @property int    MPAYTID        // length = 11
 * @property string USER_FIELD
 * @property string ORDERDESC
 * @property string CUSTOMER
 * @property string CUSTOMER_EMAIL
 * @property string LANGUAGE       // length = 2
 * @property string CUSTOMER_ID    // length = 11
 * @property string PROFILE_ID     //
 * @property string PROFILE_STATUS // IGNORED, USED, ERROR, CREATED, UPDATED, DELETED
 * @property string FILTER_STATUS
 * @property string APPR_CODE
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource Mpay24Confirmation.php
 * @license    MIT
 */
class Mpay24Confirmation
{
    /**
     * The properties, which are allowed for a transaction
     * @const CONFIRMATION_PROPERTIES
     */
    const CONFIRMATION_PROPERTIES = [
        'OPERATION'      => 'CONFIRMATION',
        'TID'            => '.{0,32}',
        'STATUS'         => '(RESERVED|BILLED|REVERSED|CREDITED|ERROR)',
        'PRICE'          => '\\d{1,11}',
        'CURRENCY'       => '[A-Z]{3}',
        'P_TYPE'         => '(CC|ELV|EPS|GIROPAY|MAESTRO|PB|PSC|QUICK|PAYPAL|MPASS|BILLPAY|KLARNA|SOFORT|MASTERPASS)',
        'BRAND'          => '(MAESTRO|MASTERPASS|MPASS|PB|PAYPAL|PSC|EPS|GIROPAY|SOFORT|QUICK|AMEX|DINERS|JCB|MASTERCARD|VISA|ATOS|B4P|HOBEX-AT|HOBEX-DE|HOBEX-NL|BILLPAY|INVOICE|HP)',
        'MPAYTID'        => '\\d{1,11}',
        'USER_FIELD'     => '.*',
        'ORDERDESC'      => '.*',
        'CUSTOMER'       => '.*',
        'CUSTOMER_EMAIL' => '.*',
        'LANGUAGE'       => '[A-Z]{2}',
        'CUSTOMER_ID'    => '.{0,11}',
        'PROFILE_ID'     => '.*',
        'PROFILE_STATUS' => '(IGNORED|USED|ERROR|CREATED|UPDATED|DELETED)',
        'FILTER_STATUS'  => '.*',
        'APPR_CODE'      => '.*',
    ];

    /**
     * An array, which contains the set properties for the confirmation object
     *
     * @property array $properties
     */
    protected $parameters = [];

    /**
     * Confirmation constructor.
     *
     * @param array $parameters
     * @param bool  $cleanUp
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $parameters, $cleanUp = false)
    {
        if ($cleanUp) {
            $parameters = self::cleanUpArray($parameters);
        }

        foreach ($parameters as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Get the parameter of the Confirmation object
     *
     * @param string $name The name of the parameter, which is searched
     *
     * @return string|bool
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        $this->isPropertyValid($name);

        return isset($this->parameters[$name]) ? $this->parameters[$name] : false;
    }

    /**
     * Set the property of the Confirmation object
     *
     * @param string $parameter The name of the property you want to set, see CONFIRMATION_PROPERTIES
     * @param mixed  $value     The value of the property you want to set
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function __set($parameter, $value)
    {
        $this->parameters[$parameter] = $this->validateValue($parameter, $value);
    }

    /**
     * Set the parameters of the Confirmation object
     *
     * @param array $parameters cleanUp the given Array to only the once valid property names, see CONFIRMATION_PROPERTIES
     *
     * @return array
     */
    public static function cleanUpArray(array $parameters)
    {
        $cleanup = [];

        foreach ($parameters as $name => $value) {
            if (array_key_exists($name, self::CONFIRMATION_PROPERTIES)) {
                $cleanup[$name] = $value;
            }
        }

        return $cleanup;
    }

    /**
     * @param $property
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function isPropertyValid($property)
    {
        if (!array_key_exists($property, self::CONFIRMATION_PROPERTIES)) {
            throw new InvalidArgumentException("The confirmation property " . $property . ", you want to get is not defined!");
        }
    }

    /**
     * @param string $property
     * @param mixed  $value
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function validateValue($property, $value)
    {
        $this->isPropertyValid($property);

        $regEx = '/^' . self::CONFIRMATION_PROPERTIES[$property] . '$/';

        if (preg_match($regEx, $value) != 1) {
            throw new InvalidArgumentException("The value " . $value . " for the property " . $property . ", is invalid");
        }

        return $value;
    }
}

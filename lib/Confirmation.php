<?php

namespace mPay24;

use InvalidArgumentException;

/**
 * The confirmation class allows you to make a basic validation of the GET parameters send by mPay24
 *
 * TYPE:   PARAMETER      - VALUE(s)
 *
 * STRING: OPERATION      - CONFIRMATION
 * STRING: TID            - length <= 32
 * STRING: STATUS         - RESERVED, BILLED, REVERSED, CREDITED, ERROR
 * INT:    PRICE          - length = 11 (e. g. "10" = "0,10")
 * STRING: CURRENCY       - length = 3 (ISO currency code, e. g. "EUR")
 * STRING: P_TYPE         - CC, ELV, EPS, GIROPAY, MAESTRO, PB, PSC, QUICK
 * STRING: BRAND          - AMEX, DINERS, JCB, MASTERCARD, VISA, ATOS, HOBEX-AT, HOBEX-DE
 * INT:    MPAYTID        - length = 11
 * STRING: USER_FIELD
 * STRING: ORDERDESC
 * STRING: CUSTOMER
 * STRING: CUSTOMER_EMAIL
 * STRING: LANGUAGE       - length = 2
 * STRING: CUSTOMER_ID    - length = 11
 * STRING: PROFILE_ID     -
 * STRING: PROFILE_STATUS - IGNORED, USED, ERROR, CREATED, UPDATED, DELETED
 * STRING: FILTER_STATUS
 * STRING: APPR_CODE
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource Confirmation.php
 * @license    MIT
 */
class Confirmation
{
	/**
	 * The properties, which are allowed for a transaction
	 * @const TRANSACTION_PROPERTIES
	 */
	const CONFIRMATION_PROPERTIES = [
		'OPERATION'      => 'CONFIRMATION',
		'TID'            => '.{0,32}',
		'STATUS'         => '(RESERVED|BILLED|REVERSED|CREDITED|ERROR)',
		'PRICE'          => '\\d{1,11}',
		'CURRENCY'       => '[A-Z]{3}',
		'P_TYPE'         => '(CC|ELV|EPS|GIROPAY|MAESTRO|PB|PSC|QUICK)',
		'BRAND'          => '(AMEX|DINERS|JCB|MASTERCARD|VISA|ATOS|HOBEX-AT|HOBEX-DE)',
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
	 * @var array $properties
	 * @throws InvalidArgumentException
	 */
	protected $parameters = [];

	public function __construct(array $parameters, $cleanUp = false)
	{
		if ($cleanUp)
		{
			$parameters = self::cleanUpArray($parameters);
		}

		foreach ($parameters as $name => $value)
		{
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

		if (isset($this->parameters[$name]))
		{
			return $this->parameters[$name];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Set the property of the Confirmation object
	 *
	 * @param string $parameter The name of the property you want to set, see TRANSACTION_PROPERTIES
	 * @param mixed  $value     The value of the property you want to set
	 *
	 * @throws InvalidArgumentException
	 */
	public function __set($parameter, $value)
	{
		$this->parameters[$parameter] = $this->validateValue($parameter, $value);
	}

	/**
	 * Set the parameters of the Confirmation object
	 *
	 * @param array $parameters cleanUp the given Array to only the once valid property names, see TRANSACTION_PROPERTIES
	 *
	 * @return array
	 */
	public static function cleanUpArray(array $parameters)
	{
		$cleanup = [];

		foreach ($parameters as $name => $value)
		{
			if (array_key_exists($name, self::CONFIRMATION_PROPERTIES))
			{
				$cleanup[$name] = $value;
			}
		}

		return $cleanup;
	}

	/**
	 * @param $property
	 *
	 * @throws InvalidArgumentException
	 */
	protected function isPropertyValid($property)
	{
		if (!array_key_exists($property, self::CONFIRMATION_PROPERTIES))
		{
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

		if (preg_match($regEx, $value) != 1)
		{
			throw new InvalidArgumentException("The value " . $value . " for the property " . $property . ", is invalid");
		}

		return $value;
	}
}

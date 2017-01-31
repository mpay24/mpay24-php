<?php

namespace mPay24;
/**
 * The autoloader class load all classes in the mPAy24 namespaces
 *
 * Class Autoloader
 * @package    mPay24
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource Autoloader.php
 * @license    MIT
 */
class Autoloader
{
	private static $registered = false;

	static public function register()
	{
		if (self::$registered === true)
		{
			return;
		}

		spl_autoload_register(array(__CLASS__, 'autoload'));

		self::$registered = true;
	}

	static public function autoload($class)
	{
		if (0 === strpos($class, 'mPay24\\'))
		{
			$fileName = __DIR__ . strtr(substr($class, 6), '\\', '/') . '.php';
			if (file_exists($fileName))
			{
				require_once $fileName;
			}
		}
	}
}

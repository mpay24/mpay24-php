<?php
/**
 * Just include this file once to have access to all classes in the mPAy24 Namespace
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource bootstrap.php
 * @license    MIT
 */

if (!class_exists('mPay24\Autoloader'))
{
	require __DIR__ . '/lib/Autoloader.php';
}

mPay24\Autoloader::register();

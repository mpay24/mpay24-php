<?php
/**
 * Just include this file once to have access to all classes in the Mpay24 Namespace
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource bootstrap.php
 * @license    MIT
 */

if (!class_exists('Mpay24\Mpay24Autoloader')) {
    require __DIR__ . '/src/Mpay24Autoloader.php';
}

Mpay24\Mpay24Autoloader::register();

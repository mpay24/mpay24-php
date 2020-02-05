<?php

namespace Mpay24Test;

/**
 * The autoloader class load all classes in the Mpay24Test namespaces
 *
 * Class Autoloader
 * @package    Mpay24Test
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource Mpay24Autoloader.php
 * @license    MIT
 */
class Mpay24Autoloader
{
    /**
     * @var bool $registered
     */
    private static $registered = false;

    /**
     * Register the autoload method
     */
    public static function register()
    {
        if (self::$registered === true) {
            return;
        }

        spl_autoload_register(array(__CLASS__, 'autoload'));

        self::$registered = true;
    }

    /**
     * @param string $class
     */
    public static function autoload($class)
    {
        if (strpos($class, 'Mpay24Test\\') === 0) {
            $fileName = __DIR__ . strtr(substr($class, 6), '\\', '/') . '.php';

            if (file_exists($fileName)) {
                require_once $fileName;
            }
        }
    }
}

<?php

namespace mPay24;

class Autoloader
{
    private static $registered = false;

    static public function register()
    {
        if (self::$registered === true) {
            return;
        }

        spl_autoload_register(array(__CLASS__, 'autoload'));

        self::$registered = true;
    }

    static public function autoload($class)
    {
        if (0 === strpos($class, 'mPay24\\')) {

            $fileName = __DIR__ . strtr(substr($class, 6), '\\', '/') . '.php';
            if (file_exists($fileName)) {
                require_once $fileName;
            }
        }
    }
}

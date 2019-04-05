<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class InvalidArgumentException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource InvalidArgumentException.php
 * @license    MIT
 */
class InvalidArgumentException extends Exception
{
    /**
     * InvalidArgumentException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = trim($message);

        parent::__construct($message, $code, $previous);
    }
}

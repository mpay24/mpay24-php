<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class MissingResponseReturnCodeException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource MissingResponseReturnCodeException.php
 * @license    MIT
 */
class MissingResponseReturnCodeException extends Exception
{
    /**
     * MissingResponseReturnCodeException constructor.
     *
     * @param string         $message
     * @param integer        $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = trim('Required field "returnCode" is missing. ' . $message);

        parent::__construct($message, $code, $previous);
    }
}

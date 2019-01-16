<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class UnauthorizedAccessException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource UnauthorizedAccessException.php
 * @license    MIT
 */
class UnauthorizedAccessException extends Exception
{
    /**
     * UnauthorizedAccessException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = trim('401 Unauthorized: check your merchant ID and password. ' . $message);

        parent::__construct($message, $code, $previous);
    }
}

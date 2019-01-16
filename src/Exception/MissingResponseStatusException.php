<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class MissingResponseStatusException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource MissingResponseStatusException.php
 * @license    MIT
 */
class MissingResponseStatusException extends Exception
{
    /**
     * MissingResponseStatusException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = trim('Required field "status" is missing. ' . $message);

        parent::__construct($message, $code, $previous);
    }
}

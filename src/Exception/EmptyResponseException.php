<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class EmptyResponseException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource EmptyResponseException.php
 * @license    MIT
 */
class EmptyResponseException extends Exception
{
    /**
     * EmptyResponseException constructor.
     *
     * @param string         $message
     * @param integer        $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = trim('The response is empty! Probably your request to mPAY24 was not sent! Please see your server log for more information! ' . $message);

        parent::__construct($message, $code, $previous);
    }
}

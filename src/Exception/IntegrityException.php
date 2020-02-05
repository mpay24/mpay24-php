<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class IntegrityException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource IntegrityException.php
 * @license    MIT
 */
class IntegrityException extends Exception
{
    /**
     * IntegrityException constructor.
     *
     * @param string         $message
     * @param integer        $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = trim($message) == '' ? 'You child class of Mpay24 did not call the parent constructor!' : trim($message);

        parent::__construct($message, $code, $previous);
    }
}

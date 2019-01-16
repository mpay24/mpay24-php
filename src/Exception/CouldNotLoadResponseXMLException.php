<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class CouldNotLoadResponseXMLException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource CouldNotLoadResponseXMLException.php
 * @license    MIT
 */
class CouldNotLoadResponseXMLException extends Exception
{
    /**
     * CouldNotLoadResponseXMLException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = trim('Could not load XML. ' . $message);

        parent::__construct($message, $code, $previous);
    }
}

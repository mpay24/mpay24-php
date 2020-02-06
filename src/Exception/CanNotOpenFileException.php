<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class CanNotOpenFileException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource CantOpenFileException.php
 * @license    MIT
 */
class CanNotOpenFileException extends Exception
{
    /**
     * CanNotOpenFileException constructor.
     *
     * @param string         $path
     * @param integer        $code
     * @param Exception|null $previous
     */
    public function __construct($path, $code = 0, Exception $previous = null)
    {
        $message = "Can't open file '" . trim($path) . "'! Please set the needed read/write rights!";

        parent::__construct($message, $code, $previous);
    }
}

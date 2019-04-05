<?php

namespace Mpay24\Exception;

use Exception;

/**
 * Class RequirementException
 * @package    Mpay24\Exception
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource RequirementException.php
 * @license    MIT
 */
class RequirementException extends Exception
{
    /**
     * RequirementException constructor.
     *
     * @param string         $details
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($details, $code = 0, Exception $previous = null)
    {
        $message = "You don't meet the needed requirements for this Application! " . trim($details);

        parent::__construct($message, $code, $previous);
    }
}

<?php

namespace Mpay24\Responses;

/**
 * The ManualClearResponse class contains the mPAYTID the basic information linked to it
 *
 * Class ManualClearResponse
 * @package    Mpay24\Responses
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource ManualClearResponse.php
 * @license    MIT
 */
class ManualClearResponse extends AbstractTransactionResponse
{
    /**
     * ManualClearResponse constructor.
     *
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoException()) {

            $this->parseResponse($this->getBody('ManualClearResponse'));
        }
    }
}

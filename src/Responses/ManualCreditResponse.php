<?php

namespace Mpay24\Responses;

/**
 * The ManualCreditResponse class contains the mPAYTID the basic information linked to it
 *
 * Class ManualCreditResponse
 * @package    Mpay24\Responses
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ManualCreditResponse.php
 * @license    MIT
 */
class ManualCreditResponse extends AbstractTransactionResponse
{
    /**
     * ManualCreditResponse constructor.
     *
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            $this->parseResponse($this->getBody('ManualCreditResponse'));
        }
    }
}

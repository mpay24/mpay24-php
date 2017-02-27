<?php

namespace Mpay24\Responses;

/**
 * The ManualReverseResponse class contains the mPAYTID the basic information linked to it
 *
 * Class ManualReverseResponse
 * @package    Mpay24\Responses
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ManualReverseResponse.php
 * @license    MIT
 */
class ManualReverseResponse extends AbstractTransactionResponse
{
    /**
     * ManualReverseResponse constructor.
     *
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            $this->parseResponse($this->getBody('ManualReverseResponse'));
        }
    }
}

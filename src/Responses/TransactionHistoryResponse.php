<?php

namespace Mpay24\Responses;

/**
 * The TransactionHistoryResponse class contains all the parameters, returned with the confirmation from mPAY24
 *
 * Class TransactionHistoryResponse
 * @package    Mpay24\Responses
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource TransactionHistoryResponse.php
 * @license    MIT
 */
class TransactionHistoryResponse extends AbstractResponse
{
    /**
     * @var int
     */
    protected $historyCount = 0;

    /**
     * @var array
     */
    protected $history = [];

    /**
     * Sets the response for the transaction History given by the response from mPAY24
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            $this->parseResponse($this->getBody('TransactionHistoryResponse'));
        }
    }

    /**
     * Get the count of all the transactions
     *
     * @return int
     */
    public function getHistoryCount()
    {
        return $this->historyCount;
    }

    /**
     * Get the parameters for a transaction, returned from mPAY24
     *
     * @return array
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Get the transaction values, returned from mPAY24
     *
     * @param int $i
     *          The index of the transaction history
     *
     * @return array|bool
     */
    public function getTransactionHistory($i)
    {
        if (isset($this->history[$i])) {
            return $this->history[$i];
        } else {
            return false;
        }
    }

    /**
     * Parse the TransactionHistoryResponse message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        $this->historyCount = (int)$body->getElementsByTagName('historyEntry')->length;

        if ($this->historyCount > 0) {

            for ($i = 0; $i < $this->historyCount; $i++) {

                $this->history[$i] = [];

                $historyEntry = $body->getElementsByTagName('historyEntry')->item($i);

                for ($j = 0; $j < $historyEntry->childNodes->length; $j++) {

                    $name  = $historyEntry->childNodes->item($j)->tagName;
                    $value = $historyEntry->childNodes->item($j)->nodeValue;

                    $this->history[$i][$name] = $value;
                }
            }
        }
    }
}

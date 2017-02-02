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
class TransactionHistoryResponse extends GeneralResponse
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

        if ($this->responseAsDom->getElementsByTagName('historyEntry')->length != 0) {

            $this->historyCount = $this->responseAsDom->getElementsByTagName('historyEntry')->length;

            for ($i = 0; $i < $this->historyCount; $i++) {

                $this->history[$i] = [];

                $transaction = $this->responseAsDom->getElementsByTagName('historyEntry')->item($i);

                for ($j = 0; $j < $transaction->childNodes->length; $j++) {

                    $this->history[$i][$transaction->childNodes->item($j)->tagName] = $transaction->childNodes->item($j)->nodeValue;
                }

            }
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
}

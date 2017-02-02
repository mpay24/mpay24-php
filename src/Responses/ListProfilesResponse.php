<?php

namespace Mpay24\Responses;

/**
 * The ListProfilesResponse class contains all the profiles, returned with the confirmation from mPAY24
 *
 * Class ListProfilesResponse
 * @package    Mpay24\Responses
 *
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource ListProfilesResponse.php
 * @license    MIT
 */
class ListProfilesResponse extends GeneralResponse
{
    /**
     * @var int
     */
    protected $profileCountSend = 0;

    /**
     * @var array
     */
    protected $profiles = [];

    /**
     * @var int
     */
    protected $totalNumber;

    /**
     * Sets the response for the profile Response given  from mPAY24
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->responseAsDom->getElementsByTagName('profile')->length != 0) {

            $this->profileCountSend = $this->responseAsDom->getElementsByTagName('profile')->length;

            for ($i = 0; $i < $this->profileCountSend; $i++) {
                $this->profiles[$i]['customerID'] = $this->responseAsDom->getElementsByTagName('customerID')->item($i)->nodeValue;
                $this->profiles[$i]['updated']    = $this->responseAsDom->getElementsByTagName('updated')->item($i)->nodeValue;
            }
        }

        $this->totalNumber = (int)$this->responseAsDom->getElementsByTagName('all')->item(0)->nodeValue;
    }

    /**
     * Get the count of all the transactions
     *
     * @return int
     */
    public function getProfileCountSend()
    {
        return $this->profileCountSend;
    }

    /**
     * Get the parameters for a transaction, returned from mPAY24
     *
     * @return array
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * Get the transaction values, returned from mPAY24
     *
     * @param int $i
     *          The index of the transaction profile
     *
     * @return array|bool
     */
    public function getProfile($i)
    {
        if (isset($this->profiles[$i])) {
            return $this->profiles[$i];
        } else {
            return false;
        }
    }

    /**
     * Get the total number of stored profiles
     *
     * @return int
     */
    public function getTotalNumber()
    {
        return $this->totalNumber;

    }
}

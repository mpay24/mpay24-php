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
class ListProfilesResponse extends AbstractResponse
{
    /**
     * @var int
     */
    protected $profileCount = 0;

    /**
     * @var array
     */
    protected $profiles = array();

    /**
     * @var int
     */
    protected $totalNumber;

    /**
     * Sets the response for the profile Response given from mPAY24
     *
     * @param string $response
     *          The SOAP response from mPAY24 (in XML form)
     */
    public function __construct($response)
    {
        parent::__construct($response);

        if ($this->hasNoError()) {

            $this->parseResponse($this->getBody('ListProfilesResponse'));
        }
    }

    /**
     * Get the count of all the transactions
     *
     * @return int
     */
    public function getProfileCount()
    {
        return $this->profileCount;
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

    /**
     * Parse the ListProfilesResponse message and save the data to the corresponding attributes
     *
     * @param \DOMElement $body
     */
    protected function parseResponse($body)
    {
        $this->totalNumber  = (int)$body->getElementsByTagName('all')->item(0)->nodeValue;
        $this->profileCount = (int)$body->getElementsByTagName('profile')->length;

        if ($this->profileCount > 0) {
            for ($i = 0; $i < $this->profileCount; $i++) {

                $profile = $body->getElementsByTagName('profile')->item($i);

                $this->profiles[$i]['customerID'] = $profile->getElementsByTagName('customerID')->item(0)->nodeValue;
                $this->profiles[$i]['updated']    = $profile->getElementsByTagName('updated')->item(0)->nodeValue;

                if ($profile->getElementsByTagName('payment')->length) {
                    $this->profiles[$i]['payment'] = $profile->getElementsByTagName('payment')->item(0)->nodeValue;
                    $this->profiles[$i]['paymentProfiles'] = $this->parsePaymentProfiles($profile->getElementsByTagName('payment'));
                }
            }
        }
    }

    /**
     * @param \DOMNodeList $paymentNodeList
     * @return array
     */
    private function parsePaymentProfiles(\DOMNodeList $paymentNodeList)
    {
        $data = array();
        foreach ($paymentNodeList as $paymentNode) {
            $data[] = $this->parseSinglePaymentProfile($paymentNode);
        }
        return $data;
    }

    /**
     * @param \DOMElement $paymentNode
     * @return array
     */
    private function parseSinglePaymentProfile(\DOMElement $paymentNode)
    {
        $data = array();
        /** @var \DOMElement $childNode */
        foreach ($paymentNode->childNodes as $childNode) {
            if ($childNode instanceof \DOMElement) {
                $data[$childNode->nodeName] = trim($childNode->textContent);
            }
        }
        return $data;
    }
}

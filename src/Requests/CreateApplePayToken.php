<?php

namespace Mpay24\Requests;

/**
 * The CreateApplePayToken class create the body for the SOAP Requests
 *
 * Class CreateApplePayToken
 * @package    Mpay24\Request
 *
 * @author     Unzer Austria GmbH <online.support.at@unzer.com>
 * @author     Stefan Polzer <develop@ps-webdesign.com>, Milko Daskalov <milko.daskalov@unzer.com>
 * @filesource CreateApplePayToken.php
 * @license    MIT
 */
class CreateApplePayToken extends AbstractRequest
{
    /**
     * @var integer
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $language;

    /**
     * @param integer $amount
     */
    public function setAmount($amount)
    {
        $this->amount = (int)$amount;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Build the Body of the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('CreateApplePayToken');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);

        $amount = $this->document->createElement('amount', $this->amount);
        $operation->appendChild($amount);

        $currency = $this->document->createElement('currency', $this->currency);
        $operation->appendChild($currency);

        if ($this->domain) {
            $domain = $this->document->createElement('domain', $this->domain);
            $operation->appendChild($domain);
        }

        if ($this->language) {
            $language = $this->document->createElement('language', $this->language);
            $operation->appendChild($language);
        }
    }
}

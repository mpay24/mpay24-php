<?php

namespace Mpay24\Requests;

use InvalidArgumentException;

/**
 * The CreatePaymentToken class create the body for te SOAP Requests
 *
 * Class CreatePaymentToken
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource CreatePaymentToken.php
 * @license    MIT
 */
class CreatePaymentToken extends AbstractRequest
{
    /**
     * @var int
     */
    protected $pType;

    /**
     * @var string
     */
    protected $templateSet;

    /**
     * @var string
     */
    protected $style;

    /**
     * @var string
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $profileId;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $language;

    /**
     * @param string $pType
     */
    public function setPType($pType)
    {
        $this->pType = $pType;
    }

    /**
     * @param string $templateSet
     */
    public function setTemplateSet($templateSet)
    {
        $this->templateSet = $templateSet;
    }

    /**
     * @param string $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @param string $profileId
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
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
     * @param array $additional
     */
    public function setAdditional($additional)
    {
        isset($additional['templateSet']) ? $this->setTemplateSet($additional['templateSet']) : null;
        isset($additional['style']) ? $this->setStyle($additional['style']) : null;
        isset($additional['customerID']) ? $this->setCustomerId($additional['customerID']) : null;
        isset($additional['profileID']) ? $this->setProfileId($additional['profileID']) : null;
        isset($additional['domain']) ? $this->setDomain($additional['domain']) : null;
        isset($additional['language']) ? $this->setLanguage($additional['language']) : null;
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    protected function build()
    {
        $operation = $this->buildOperation('CreatePaymentToken');

        $merchantID = $this->document->createElement('merchantID', $this->merchantId);
        $operation->appendChild($merchantID);


        $pType = $this->document->createElement('pType', $this->pType);
        $operation->appendChild($pType);

        if ($this->templateSet) {
            $templateSet = $this->document->createElement('templateSet', $this->templateSet);
            $operation->appendChild($templateSet);
        }

        if ($this->style) {
            $style = $this->document->createElement('style', $this->style);
            $operation->appendChild($style);
        }

        if ($this->customerId) {
            $customerId = $this->document->createElement('customerID', $this->customerId);
            $operation->appendChild($customerId);
        }

        if ($this->profileId) {
            $profileId = $this->document->createElement('profileID', $this->profileId);
            $operation->appendChild($profileId);
        }

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

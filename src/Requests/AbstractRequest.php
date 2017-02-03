<?php

namespace Mpay24\Requests;

use DOMDocument;
use DOMNode;

/**
 * The AbstractRequest class create the basic envelope for te SOAP Requests
 *
 * Class AbstractRequest
 * @package    Mpay24\Request
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @author     Stefan Polzer <develop@posit.at>
 * @filesource AbstractResponse.php
 * @license    MIT
 */
abstract class AbstractRequest
{
    const XML_NS = 'http://www.w3.org/2000/xmlns/';
    const SOAP_ENVELOPE = 'http://schemas.xmlsoap.org/soap/envelope/';
    const ETP_NAME_SPACE = 'https://www.mpay24.com/soap/etp/1.5/ETP.wsdl';

    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var int
     */
    protected $merchantId;

    /**
     * AbstractRequest constructor.
     *
     * @param $merchantId
     */
    public function __construct($merchantId)
    {
        $this->merchantId = $merchantId;

        $this->document = $this->buildEnvelope();
    }

    /**
     * Build the Body ot the Request and add it to $this->document
     */
    abstract protected function build();

    /**
     * @return string return the Request in XML Fromat
     */
    public function getXml()
    {
        $this->build();

        return $this->document->saveXML();
    }

    /**
     * Create a DOMDocument and prepare it for SOAP request: set Envelope, NameSpaces, create empty Body
     *
     * @return DOMDocument
     */
    protected function buildEnvelope()
    {
        $soap_xml = new DOMDocument("1.0", "UTF-8");

        $soap_xml->formatOutput = true;

        $envelope = $soap_xml->createElementNS(self::SOAP_ENVELOPE, 'SOAP-ENV:Envelope');
        $envelope->setAttributeNS(self::XML_NS, 'xmlns:etp', self::ETP_NAME_SPACE);
        $envelope->setAttributeNS(self::XML_NS, 'xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $envelope->setAttributeNS(self::XML_NS, 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        $envelope = $soap_xml->appendChild($envelope);

        $body = $soap_xml->createElementNS(self::SOAP_ENVELOPE, 'SOAP-ENV:Body');

        $envelope->appendChild($body);

        return $soap_xml;
    }

    /**
     * @param string $operation
     *
     * @return DOMNode
     */
    protected function buildOperation($operation)
    {
        $body      = $this->document->getElementsByTagNameNS(self::SOAP_ENVELOPE, 'Body')->item(0);
        $operation = $this->document->createElementNS(self::ETP_NAME_SPACE, 'etp:' . $operation);

        return $body->appendChild($operation);
    }

    /**
     * @param DOMNode $parent
     * @param array   $list
     */
    protected function appendArray(DOMNode &$parent, array &$list)
    {
        foreach ($list as $name => $value) {
            if (is_array($value)) {
                $element = $this->document->createElement($name);
                $this->appendArray($element, $value);
                $parent->appendChild($element);
            } else {
                $element = $this->document->createElement($name, $value);
                $parent->appendChild($element);
            }
        }
    }
}

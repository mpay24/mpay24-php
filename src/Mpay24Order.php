<?php

namespace Mpay24;

use DOMDocument;
use DOMElement;
use DOMXPath;
use InvalidArgumentException;

/**
 * The ORDER class provides the functioanallity to create a XML, which is validatable with the MDXI.xsd
 *
 * Class Mpay24Order
 * @package    Mpay24
 *
 * @author     mPAY24 GmbH <support@mpay24.com>
 * @filesource Mpay24Order.php
 * @license    MIT
 */
class Mpay24Order
{
    /**
     * The DOMDocument, which the MDXI XML will be build on
     *
     * @var DOMDocument
     */
    protected $document;

    /**
     * A DOMNode from the MDXI XML, or the whole MDXI XML, represented as DOMDocument
     *
     * @var DOMElement
     */
    protected $node;

    /**
     * Create a DOMDocument or a ORDER-Object with root $doc
     *
     * @param DOMDocument $document The root DOMNode of an XML tree
     * @param DOMElement  $node     The child DOMNode
     */
    public function __construct($document = null, $node = null)
    {
        if (!is_a($document, DOMDocument::class)) {
            $document = new DOMDocument("1.0", "UTF-8");

            $document->formatOutput = true;
        }

        $this->document = $document;

        $this->node = is_a($node, DOMElement::class) ? $node : $this->document;
    }

    /**
     * Generic call-Method instead of numerous setter methods
     *
     * @param string $method The name of the method, which is called for the Item-Object
     * @param array  $args
     *                       The arguments with them the method is called - minOccurance = 0, maxOccurance = 2:
     *                       The first argument must be a positive integer (will be used as a index)
     *                       The second argument is optional and would be used as value for the DOMNode
     *
     * @return Mpay24Order
     *
     * @throws InvalidArgumentException
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == "set" && isset($args[0])) {
            $attributeName = substr($method, 3);

            $value = $args[0];

            if ($this->isDecimalAttribute($attributeName)) {
                $value = $this->formatDecimal($value);
            }

            $this->node->setAttribute($attributeName, $value);
            return $this;
        } elseif (isset($args[0])) {
            if (sizeof($args) > 2) {
                throw new InvalidArgumentException("It is not allowed to set more than 2 arguments for the node '$method'!");
            }
            if (!is_int($args[0]) || $args[0] < 1) {
                throw new InvalidArgumentException("The first argument for the node '$method' must be whole number, bigger than 0!");
            }

            $name = $method . '[' . $args[0] . ']';

            $xpath = new DOMXPath($this->document);
            $query = $xpath->query($name, $this->node);

            if ($query->length > 0) {
                return new Mpay24Order($this->document, $query->item(0));
            } else {
                if (isset($args[1])) {
                    $value = $args[1];

                    if ($this->isDecimalElement($method)) {
                        $value = $this->formatDecimal($value);
                    }

                    $node = $this->createTextElement($method, $value);
                } else {
                    $node = $this->document->createElement($method);
                }

                $node = $this->node->appendChild($node);

                return new Mpay24Order($this->document, $node);
            }
        }

        throw new InvalidArgumentException("At least one Argument must be provided for node '$method'!");
    }

    /**
     * Get the value of a ORDER-Variable
     *
     * @param string $name The name of the method, which is called for the Item-Object
     *
     * @return Mpay24Order
     */
    public function __get($name)
    {
        $xpath = new DOMXPath($this->document);
        $query = $xpath->query($name, $this->node);

        if ($query->length > 0) {
            return new Mpay24Order($this->document, $query->item(0));
        } else {
            $node = $this->document->createElement($name);
            $node = $this->node->appendChild($node);

            return new Mpay24Order($this->document, $node);
        }
    }

    /**
     * Set the value of a ORDER-Variable
     *
     * @param string $name  The name of the Node you want to set
     * @param mixed  $value The value of the Node you want to set
     */
    public function __set($name, $value)
    {
        $xpath = new DOMXPath($this->document);
        $query = $xpath->query($name, $this->node);

        if ($this->isDecimalElement($name)) {
            $value = $this->formatDecimal($value);
        }

        if ($query->length > 0) {
            if ($this->isAlreadyXMLEncoded($value)) {
                $query->item(0)->nodeValue = $value;
            } else {
                $query->item(0)->textContent = $value;
            }
        } else {
            $element = $this->createTextElement($name,$value);
            $this->node = $this->node->appendChild($element);
        }
    }

    /**
     * Create a XML-Object from the ORDER-Object and return it
     * @return string
     */
    public function toXML()
    {
        return $this->document->saveXML();
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    protected function isDecimalAttribute($name)
    {
        switch ($name) {
            case 'Tax':
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return DOMElement element created
     */
    protected function createTextElement($name, $value)
    {
        if ($this->isAlreadyXMLEncoded($value)) {
            return $this->document->createElement($name, $value);
        }
        $element = $this->document->createElement($name);
        if (!empty($value)) {
            $txtnode = $this->document->createTextNode($value);
            $element->appendChild($txtnode);
        }
        return $element;
    }

    /**
     * @param string $source
     *
     * @return boolean
     */
    protected function isAlreadyXMLEncoded($source)
    {
        $encoded = htmlentities($source, ENT_XML1, "UTF-8", false); // detect if already encoded
        return ($source === $encoded); // already encoded
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    protected function isDecimalElement($name)
    {
        switch ($name) {
            case 'Price':
            case 'ItemPrice':
            case 'SubTotal':
            case 'Discount':
            case 'ShippingCosts':
            case 'Tax':
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string|integer|float $value
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function formatDecimal($value)
    {
        switch (gettype($value)) {
            case 'integer':
                break;
            case 'double':
                break;
            case 'string':
                if (preg_match('/\b[0-9]+,[0-9]+\b/', $value, $match)) {
                    $value = str_replace(',', '.', $match[0]);
                }
                break;
            default:
                throw new InvalidArgumentException('A value of the type "' . gettype($value) . '" can not converted into a decimal.');
        }

        return number_format(floatval($value), 2, '.', '');
    }
}

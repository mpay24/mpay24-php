<?php

namespace Mpay24;

use DOMDocument;
use DOMNode;
use DOMXPath;

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
    protected $doc;

    /**
     * A DOMNode from the MDXI XML, or the whole MDXI XML, represented as DOMDocument
     *
     * @var DOMDocument|DOMNode
     */
    protected $node;

    /**
     * Create a DOMDocument or a ORDER-Object with root $doc
     *
     * @param DOMNode $doc  The root DOMNode of an XML tree
     * @param DOMNode $node The child DOMNode
     */
    public function __construct($doc = null, $node = null)
    {
        if ($doc) {
            $this->doc = $doc;
        } else {
            $this->doc               = new DOMDocument("1.0", "UTF-8");
            $this->doc->formatOutput = true;
        }

        if ($node) {
            $this->node = $node;
        } else {
            $this->node = $this->doc;
        }
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
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == "set" && $args[0] != '') {
            $attributeName = substr($method, 3);

            $value = $args[0];
            $match = [];

            if($attributeName != 'Description') {
                if (preg_match('/\b[0-9]+,[0-9]+\b/', $value, $match)) {
                    $value = str_replace(',', '.', $match[0]);
                }
            }

            if (preg_match('/\b[0-9]+.[0-9]+\b/', $value, $match) &&
                $value == $match[0] &&
                $attributeName != 'shippingCosts' &&
                (is_int(strpos($attributeName, 'price')) ||
                    is_int(strpos($attributeName, 'Price')) ||
                    is_int(strpos($attributeName, 'Tax')) ||
                    is_int(strpos($attributeName, 'cost')) ||
                    is_int(strpos($attributeName, 'Cost'))
                )
            ) {
                $value = number_format(floatval($match[0]), 2, '.', '');
            }

            $this->node->setAttribute($attributeName, $value);
        } elseif ($args[0] != '') {
            if (sizeof($args) > 2) {
                die("It is not allowed to set more than 2 arguments for the node '$method'!");
            }
            if (!is_int($args[0]) || $args[0] < 1) {
                die("The first argument for the node '$method' must be whole number, bigger than 0!");
            }

            $name = $method . '[' . $args[0] . ']';

            $xpath = new DOMXPath($this->doc);
            $qry   = $xpath->query($name, $this->node);

            if ($qry->length > 0) {
                return new Mpay24Order($this->doc, $qry->item(0));
            } else {
                if (array_key_exists(1, $args)) {
                    $value = $args[1];

                    if (preg_match('/\b[0-9]+,[0-9]+\b/', $value, $match)) {
                        $value = str_replace(',', '.', $match[0]);
                    }

                    if (preg_match('/\b[0-9]+.[0-9]+\b/', $value, $match) &&
                        $value == $match[0] &&
                        $name != 'shippingCosts' &&
                        (is_int(strpos($name, 'price')) ||
                            is_int(strpos($name, 'Price')) ||
                            is_int(strpos($name, 'Tax')) ||
                            is_int(strpos($name, 'cost')) ||
                            is_int(strpos($name, 'Cost'))
                        )
                    ) {
                        $value = number_format(floatval($match[0]), 2, '.', '');
                    }

                    $node = $this->doc->createElement($method, $value);
                } else {
                    $node = $this->doc->createElement($method);
                }

                $node = $this->node->appendChild($node);

                return new Mpay24Order($this->doc, $node);
            }
        }
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
        $xpath = new DOMXPath($this->doc);
        $qry   = $xpath->query($name, $this->node);

        if ($qry->length > 0) {
            return new Mpay24Order($this->doc, $qry->item(0));
        } else {
            $node = $this->doc->createElement($name);
            $node = $this->node->appendChild($node);

            return new Mpay24Order($this->doc, $node);
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
        $xpath = new DOMXPath($this->doc);
        $qry   = $xpath->query($name, $this->node);

        $value = str_replace('&', '&amp;', $value);

        if (preg_match('/\b[0-9]+,[0-9]+\b/', $value, $match)) {
            $value = str_replace(',', '.', $match[0]);
        }

        if (preg_match('/\b[0-9]+.[0-9]+\b/', $value, $match) &&
            $value == $match[0] &&
            $name != 'shippingCosts' &&
            (is_int(strpos($name, 'price')) ||
                is_int(strpos($name, 'Price')) ||
                is_int(strpos($name, 'Tax')) ||
                is_int(strpos($name, 'cost')) ||
                is_int(strpos($name, 'Cost'))
            )
        ) {
            $value = number_format(floatval($match[0]), 2, '.', '');
        }

        if (strpos($value, "<") || strpos($value, ">")) {
            $value = "<![CDATA[" . $this->xmlEncode($value) . "]]>";
        }

        if ($qry->length > 0) {
            $qry->item(0)->nodeValue = $value;
        } else {
            $node       = $this->doc->createElement($name, $value);
            $this->node = $this->node->appendChild($node);
        }
    }

    /**
     * Create a XML-Object from the ORDER-Object and return it
     * @return string
     */
    public function toXML()
    {
        return $this->doc->saveXML();
    }

    /**
     * Validate the ORDER with the schema, defined in the constant MDXI_SCHEMA and return TRUE if the validation was successful or FALSE
     *
     * @return bool
     *
     * @deprecated 5.0.0
     */
    public function validate()
    {
        return true;
    }

    /**
     * Encode the XML-characters in a string
     *
     * @param string $txt A string to be encoded
     *
     * @return string
     */
    protected function xmlEncode($txt)
    {
        $txt = str_replace('<', '&lt;', $txt);
        $txt = str_replace('>', '&gt;', $txt);
        $txt = str_replace('&amp;apos;', "'", $txt);
        $txt = str_replace('&amp;quot;', '"', $txt);

        return $txt;
    }
}

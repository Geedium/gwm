<?php

namespace GMW\Core;

/**
 * Undocumented class
 * 
 * @version 1.0.0
 * @deprecated *
 */
class DOM extends Reader
{
    private $document;

    /**
     * @magic
     */
    function __construct()
    {
        $this->document = new DomDocument('1.0');
    }

    /**
     * @magic
     */
    function __toString()
    {
        return $this->document->saveHTML();
    }

    /**
     * Undocumented function
     *
     * @return DOM
     */
    function Load() : DOM
    {
        @$this->document->loadHTML($this->text);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param string $value
     * @return DOMElement
     * @since 1.0.0
     */
    function Add(string $name, string $value)
    {
        return $this->document->createElement($name, $value);
    }

    /**
     * Undocumented function
     *
     * @param DOMNode $key
     * @param DOMNode $value
     * @return DOMElement
     * @since 1.0.0
     */
    function Append(DOMNode $key, DOMNode $value) : DOMElement
    {
        return $key->appendChild($value);
    }

    /**
     * Undocumented function
     *
     * @param string $id
     * @param string $value
     * @return DOMElement|null
     * @since 1.0.0
     */
    function Override(string $id, string $value) :? DOMElement
    {
        $element = @$this->document->getElementById($id);
        $element->nodeValue = null;
        $fragment = $this->document->createDocumentFragment();
        $fragment->appendXML($value);
        $element->appendChild($value);
        return $element;
    }
}
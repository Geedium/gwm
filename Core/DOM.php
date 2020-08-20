<?php

namespace GMW\Core;

/**
 * Undocumented class
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
     * @deprecated
     */
    function Load()
    {
        @$this->document->loadHTML($this->text);
    }

    /**
     * @magic
     */
    function __toString()
    {
        return $this->document->saveHTML();
    }
}
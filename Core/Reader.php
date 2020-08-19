<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Reader
{
    private string $text = '';

    /**
     * @magic
     */
    function __construct($path)
    {
        \ob_start();
        @readfile($path, true);
        $this->text = \ob_get_clean();
    }

    /**
     * @magic
     */
    function __toString()
    {
        return $this->text;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param string $value
     * @return Reader
     */
    public function Replace(string $key, string $value) : Reader
    {
        $this->text = str_replace($key, $value, $this->text);
        return $this;
    }
    
    /**
     * Undocumented function
     *
     * @param string $key
     * @param Reader $value
     * @return Reader
     */
    public function Merge(string $key, Reader $value) : Reader
    {
        return $this->Replace($key, $value);
    }
}
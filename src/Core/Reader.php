<?php

namespace GWM\Core;

/**
 * Undocumented class
 * 
 * @version 1.0.0
 */
class Reader
{
    protected string $text = '';

    /**
     * @magic
     */
    function __construct($path = '')
    {
        if ($path != '') {
            \ob_start("ob_gzhandler");

            // Prevent XSS (Cross-site Scripting)
            htmlspecialchars(@readfile($path, true), ENT_QUOTES, 'UTF-8');

            $this->text = \ob_get_clean();
        }
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
     * @param string $subject
     * @return array|string
     * @since 1.0.0
     */
    public static function Sanitize($subject)
    {
        return \preg_replace([
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
            '/<!--(.|\s)*?-->/'
        ], ['>', '<', '\\1', ''], $subject);
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param string|null $value
     * @return Reader
     * @since 1.0.0
     */
    public function Replace(string $key, ?string $value) : Reader
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
     * @since 1.0.0
     */
    public function Merge(string $key, Reader $value) : Reader
    {
        return $this->Replace("<!--$key-->", $value);
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param Reader $value
     * @return Reader
     * @since 1.0.0
     */
    public function Concatenate(Reader $value) : Reader
    {
        $this->text .= $value;
        return $this;
    }
}
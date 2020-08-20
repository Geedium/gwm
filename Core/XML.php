<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class XML
{
    protected $path, $version, $encoding, $xml, $doc;

    /**
     * @magic
     */
    function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Undocumented function
     *
     * @param [type] $file
     * @return void
     */
    public function Load($file)
    {
        $this->path = "$this->path/$file.xml";
        if (is_file($this->path) == true) {
            $this->doc = new DOMDocument();
            $this->doc->preserveWhiteSpace = false;
            $this->doc->substituteEntities = true;
            $this->doc->load($this->path);
            $this->version = $this->doc->xmlVersion;
            $this->encoding = $this->doc->xmlEncoding;
            $this->xml = $this->doc->saveXML();
            unset($this->doc);
        } else {
            die('Failed to load XML file!');
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function Parse()
    {
        $this->doc = \simplexml_load_file($this->xml, null, LIBXML_NOERROR);
        return @json_decode(@json_encode($this->doc), 1);
    }
}
